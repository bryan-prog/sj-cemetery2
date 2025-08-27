<?php

namespace App\Http\Controllers;

use App\Models\{ Renewal, Slot, Reservation };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class RenewalPermitController extends Controller
{
    private const PENALTY_PER_YEAR = 100.00;

    public function index(Request $request)
    {
        $raw = $request->query('status', 'pending');
        $allowed = ['pending','renewed','approved','all'];
        $status = in_array($raw, $allowed, true) ? $raw : 'pending';

        $q = Renewal::with([
            'slot.cell.level.apartment',
            'deceased',
        ])->latest('id');

        switch ($status) {
            case 'pending':
                $q->where('status', 'pending');
                break;
            case 'renewed':
            case 'approved':
                $q->whereIn('status', ['approved']);
                break;
            case 'all':
                $q->whereIn('status', ['pending','approved','denied']);
                break;
        }

        $renewals = $q->get();

        if ($status === 'approved') $status = 'renewed';

        return view('renewals.index', compact('renewals','status'));
    }

    public function show(Renewal $renewal)
    {
        $renewal->load(['slot.cell.level.apartment', 'deceased', 'reservation']);

        $apt   = optional(optional(optional(optional($renewal->slot)->cell)->level)->apartment);
        $cell  = optional(optional($renewal->slot)->cell);
        $level = optional($cell->level);

        $buriedAt = ($apt && $level && $cell && $renewal->slot)
            ? $apt->name.' Level '.$level->level_no.' R'.$cell->row_no.' C'.$cell->col_no.' S'.$renewal->slot->slot_no
            : '—';

        $d = $renewal->deceased;
        $deceasedName = $d
            ? trim(sprintf(
                '%s%s%s%s',
                $d->last_name ? strtoupper($d->last_name) : '',
                $d->first_name ? (', '.strtoupper($d->first_name)) : '',
                $d->middle_name ? (' '.strtoupper($d->middle_name)) : '',
                $d->suffix ? (' '.strtoupper($d->suffix)) : ''
            ))
            : '—';

        $pen = $this->computePenaltyForReservation(
            $renewal->reservation_id,
            $renewal->renewal_start ? Carbon::parse($renewal->renewal_start) : null,
            $ignoreRenewalId = $renewal->id
        );

        return response()->json([
            'id'                       => $renewal->id,
            'status'                   => $renewal->status,
            'requesting_party'         => $renewal->requesting_party,
            'applicant_address'        => $renewal->applicant_address,
            'contact'                  => $renewal->contact,
            'relationship_to_deceased' => $renewal->relationship_to_deceased,
            'date_applied'             => optional($renewal->date_applied)->toDateString(),
            'renewal_start'            => optional($renewal->renewal_start)->toDateString(),
            'renewal_end'              => optional($renewal->renewal_end)->toDateString(),
            'amount_as_per_ord'        => $renewal->amount_as_per_ord,
            'remarks'                  => $renewal->remarks,
            'deceased_name'            => $deceasedName,
            'sex'                      => optional($renewal->deceased)->sex ?? '—',
            'buried_at'                => $buriedAt,
            'penalty_years'            => $pen['years'],
            'penalty_amount'           => number_format($pen['amount'], 2, '.', ''),
        ]);
    }

    public function update(Request $r, Renewal $renewal)
    {
        // Allow editing for both pending and approved (renewed)
        abort_unless(in_array($renewal->status, ['pending','approved'], true), 400, 'Only pending or renewed renewals can be edited.');

        $data = $r->validate([
            'requesting_party'         => 'required|string|max:255',
            'applicant_address'        => 'required|string|max:255',
            'contact'                  => 'required|string|max:55',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'date_applied'             => 'required|date',

            'renewal_start'            => 'nullable',
            'renewal_end'              => 'nullable',
            'amount_as_per_ord'        => 'nullable|numeric|min:0',
            'remarks'                  => 'nullable|string|max:500',
        ]);

        $slot   = Slot::with('cell')->findOrFail($renewal->slot_id);
        $cellId = (int) optional($slot)->grave_cell_id;

        [$pStart, $pEnd] = $this->resolvePeriod($data, $cellId, Carbon::parse($data['date_applied'])->startOfDay());

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');
        $dup = Renewal::whereIn('slot_id', $slotIds)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('renewal_start', $pStart->toDateString())
            ->whereDate('renewal_end',   $pEnd->toDateString())
            ->where('id','!=',$renewal->id)
            ->exists();

        if ($dup) {
            throw ValidationException::withMessages([
                'renewal_start' => ["A renewal for this cell and period ({$pStart->format('Y-m-d')} to {$pEnd->format('Y-m-d')}) already exists (pending or approved)."],
            ]);
        }

        $pen = $this->computePenaltyForReservation($renewal->reservation_id, $pStart);

        $data['renewal_start'] = $pStart->toDateString();
        $data['renewal_end']   = $pEnd->toDateString();

        $penLine = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
        $data['remarks'] = trim(($data['remarks'] ?? '').' '.$penLine);

        $renewal->update($data);

        return response()->json([
            'message'  => 'Renewal updated.',
            'payload'  => [
                'requesting_party' => $renewal->requesting_party,
                'relationship'     => $renewal->relationship_to_deceased ?? '—',
                'deceased_name'    => optional($renewal->deceased)->last_name ? (strtoupper($renewal->deceased->last_name).', '.strtoupper($renewal->deceased->first_name ?? '')) : '—',
                'buried_at'        => $renewal->buried_at ?? '—',
                'period'           => optional($renewal->renewal_start)->format('Y').' – '.optional($renewal->renewal_end)->format('Y'),
                'penalty_years'    => $pen['years'],
                'penalty_amount'   => number_format($pen['amount'], 2, '.', ''),
            ],
        ]);
    }

    public function store(Request $r)
    {
        $v = $r->validate([
            'reservation_id'   => 'required|exists:reservations,id',
            'slot_id'          => 'required|exists:slots,id',
            'date_applied'     => 'required|date',

            'renewal_start'    => 'nullable',
            'renewal_end'      => 'nullable',
            'requesting_party' => 'required|string|max:255',
            'applicant_address'=> 'required|string|max:255',
            'contact'          => 'required|string|max:55',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'amount_as_per_ord'=> 'nullable|numeric|min:0',
            'verifiers_id'     => 'nullable|exists:verifiers,id',
            'remarks'          => 'nullable|string|max:500',
        ]);

        $seedSlot = Slot::with('cell')->findOrFail($v['slot_id']);
        $cellId   = (int) optional($seedSlot)->grave_cell_id;
        $cell     = optional($seedSlot)->cell;

        $pendingExists = Renewal::whereIn('slot_id',
                Slot::where('grave_cell_id', $cellId)->pluck('id')
            )->where('status', 'pending')->exists();

        if ($pendingExists) {
            return back()->with('error', 'A pending renewal already exists for this cell.');
        }

        [$pStart, $pEnd] = $this->resolvePeriod($v, $cellId, Carbon::parse($v['date_applied'])->startOfDay());

        $slotIdsAll = Slot::where('grave_cell_id', $cellId)->pluck('id');
        $dup = Renewal::whereIn('slot_id', $slotIdsAll)
            ->whereIn('status', ['pending','approved'])
            ->whereDate('renewal_start', $pStart->toDateString())
            ->whereDate('renewal_end',   $pEnd->toDateString())
            ->exists();

        if ($dup) {
            return back()->with(
                'error',
                'A renewal for this cell and period ('.$pStart->format('Y-m-d').' to '.$pEnd->format('Y-m-d').') already exists.'
            );
        }

        $v['renewal_start'] = $pStart->toDateString();
        $v['renewal_end']   = $pEnd->toDateString();

        $isFamilyOwned = !is_null(optional($cell)->family_id);

        DB::transaction(function () use ($v, $cellId, $isFamilyOwned) {
            if ($isFamilyOwned) {
                $slotIdsInCell = Slot::where('grave_cell_id', $cellId)->pluck('id');

                $reservations = Reservation::active()
                    ->whereIn('slot_id', $slotIdsInCell)
                    ->with('slot')
                    ->get();

                $slotIdsToMark = $reservations->pluck('slot_id')->filter()->unique()->values();
                if ($slotIdsToMark->isNotEmpty()) {
                    Slot::lockForUpdate()
                        ->whereIn('id', $slotIdsToMark)
                        ->update(['status' => 'renewal_pending']);
                }

                foreach ($reservations as $res) {
                    $payload                    = $v;
                    $payload['reservation_id']  = $res->id;
                    $payload['slot_id']         = $res->slot_id;

                    $pen = $this->computePenaltyForReservation($res->id, Carbon::parse($v['renewal_start']));
                    $penLine = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
                    $payload['remarks'] = trim(($payload['remarks'] ?? '') . ' [BULK FAMILY CELL] ' . $penLine);

                    Renewal::create($payload);
                }

                if ($reservations->isEmpty()) {
                    Slot::lockForUpdate()->where('id', $v['slot_id'])
                        ->update(['status' => 'renewal_pending']);

                    $pen = $this->computePenaltyForReservation($v['reservation_id'], Carbon::parse($v['renewal_start']));
                    $penLine = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
                    $v['remarks'] = trim(($v['remarks'] ?? '') . ' [BULK FAMILY CELL] ' . $penLine);

                    Renewal::create($v);
                }
            } else {
                Slot::lockForUpdate()
                    ->where('id', $v['slot_id'])
                    ->update(['status' => 'renewal_pending']);

                $pen = $this->computePenaltyForReservation($v['reservation_id'], Carbon::parse($v['renewal_start']));
                $penLine = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
                $v['remarks'] = trim(($v['remarks'] ?? '') . ' ' . $penLine);

                Renewal::create($v);
            }
        });

        if ($isFamilyOwned) {
            $count = Renewal::whereIn('slot_id', function ($q) use ($cellId) {
                    $q->select('id')->from('slots')->where('grave_cell_id', $cellId);
                })
                ->where('status', 'pending')
                ->whereDate('renewal_start', $v['renewal_start'])
                ->whereDate('renewal_end',   $v['renewal_end'])
                ->count();

            return back()->with('success', "Renewal requests lodged for {$count} slot(s) in this family-owned cell.");
        }

        return back()->with('success','Renewal request lodged!');
    }

    public function approve(Request $request, Renewal $renewal)
    {
        abort_if($renewal->status !== 'pending', 400, 'Already processed');

        $data = $request->validate([
            'or_number'    => 'required|string|max:50',
            'or_issued_at' => 'required|date',
        ]);

        DB::transaction(function () use ($renewal, $data) {
            $renewal->update(array_merge($data, [
                'status'  => 'approved',
                'remarks' => trim(($renewal->remarks ?: '') . ' Approved ' . now()->toDateTimeString()),
            ]));

            $renewal->slot?->update(['status' => 'occupied']);
        });

        return back()->with('success', 'Renewal approved.');
    }

    public function deny(Renewal $renewal)
    {
        abort_if($renewal->status!=='pending',400,'Already processed');

        DB::transaction(function () use ($renewal) {
            $renewal->update([
                'status'=>'denied',
                'remarks'=>trim(($renewal->remarks ?: '') . ' Denied '.now()),
            ]);

            $renewal->slot()->update(['status'=>'occupied']);
        });

        return back()->with('success','Renewal denied.');
    }

    public function approveBatch(Request $request, Renewal $renewal)
    {
        $data = $request->validate([
            'or_number'    => 'required|string|max:50',
            'or_issued_at' => 'required|date',
        ]);

        $seedSlot = Slot::with('cell')->findOrFail($renewal->slot_id);
        $cellId   = (int) optional($seedSlot)->grave_cell_id;

        $batch = Renewal::query()
            ->where('status', 'pending')
            ->whereIn('slot_id', function ($q) use ($cellId) {
                $q->select('id')->from('slots')->where('grave_cell_id', $cellId);
            })
            ->orderBy('id')
            ->get();

        if ($batch->isEmpty()) {
            return back()->with('info', 'No pending renewals to approve for this cell.');
        }

        DB::transaction(function () use ($batch, $data) {
            foreach ($batch as $r) {
                $r->update(array_merge($data, [
                    'status'  => 'approved',
                    'remarks' => trim(($r->remarks ?: '') . ' Approved ' . now()->toDateTimeString()),
                ]));

                if ($r->slot_id) {
                    Slot::lockForUpdate()
                        ->where('id', $r->slot_id)
                        ->update(['status' => 'occupied']);
                }
            }
        });

        return back()->with('success', 'Approved ' . $batch->count() . ' renewal request(s) for this cell.');
    }

    private function resolvePeriod(array $payload, ?int $cellId, Carbon $ref): array
    {
        $startRaw = $payload['renewal_start'] ?? null;
        $endRaw   = $payload['renewal_end']   ?? null;

        if ($startRaw || $endRaw) {
            $start = $startRaw ? $this->parseYearOrDate($startRaw) : null;
            $end   = $endRaw   ? $this->parseYearOrDate($endRaw)   : null;

            if ($start && !$end) {
                $end = $start->copy()->addYears(5);
            } elseif (!$start && $end) {
                $end   = $end->copy();
                $start = $end->copy()->subYears(5);
            }

            if ($end->ne($start->copy()->addYears(5))) {
                throw ValidationException::withMessages([
                    'renewal_end' => ['Renewal period must be exactly 5 years (end = start + 5 years).'],
                ]);
            }

            return [$start->startOfDay(), $end->startOfDay()];
        }

        $anchor = $this->getCellAnchorDate($cellId);
        return $this->computeFiveYearPeriod($anchor, $ref);
    }

    private function parseYearOrDate(string $value): Carbon
    {
        $value = trim($value);
        if (preg_match('/^\d{4}$/', $value)) {
            return Carbon::createFromDate(((int)$value), 1, 1)->startOfDay();
        }
        try {
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable $e) {
            throw ValidationException::withMessages([
                'renewal_start' => ['Invalid date/year format. Use YYYY or YYYY-MM-DD.'],
            ]);
        }
    }

    private function computePenaltyForReservation(?int $reservationId, ?Carbon $newStart, ?int $ignoreRenewalId = null): array
    {
        if (!$reservationId || !$newStart) {
            return ['years' => 0, 'amount' => 0.0];
        }

        $latestApprovedEnd = Renewal::where('reservation_id', $reservationId)
            ->where('status', 'approved')
            ->when($ignoreRenewalId, fn($q) => $q->where('id', '!=', $ignoreRenewalId))
            ->whereDate('renewal_end', '<=', $newStart->toDateString())
            ->max('renewal_end');

        if ($latestApprovedEnd) {
            $coverageEnd = Carbon::parse($latestApprovedEnd)->startOfDay();
        } else {
            $res = Reservation::find($reservationId);
            if ($res && $res->internment_sched) {
                $coverageEnd = Carbon::parse($res->internment_sched)->startOfDay()->addYears(5);
            } elseif ($res && $res->date_applied) {
                $coverageEnd = Carbon::parse($res->date_applied)->startOfDay()->addYears(5);
            } else {
                $coverageEnd = $newStart->copy();
            }
        }

        if ($newStart->lte($coverageEnd)) {
            return ['years' => 0, 'amount' => 0.0];
        }

        $years = $coverageEnd->diffInYears($newStart);
        $amount = $years * self::PENALTY_PER_YEAR;

        return ['years' => $years, 'amount' => $amount];
    }

    private function getCellAnchorDate(?int $cellId): Carbon
    {
        if (!$cellId) return now()->startOfDay();

        $earliestOcc = Slot::where('grave_cell_id', $cellId)
            ->whereNotNull('occupancy_start')
            ->min('occupancy_start');

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        $earliestInternment = null;
        if ($slotIds->isNotEmpty()) {
            $earliestInternment = Reservation::whereIn('slot_id', $slotIds)
                ->whereNotNull('internment_sched')
                ->min('internment_sched');
        }

        $earliestRenewal = null;
        if ($slotIds->isNotEmpty()) {
            $earliestRenewal = Renewal::whereIn('slot_id', $slotIds)
                ->whereNotNull('renewal_start')
                ->min('renewal_start');
        }

        $candidates = array_filter([$earliestOcc, $earliestInternment, $earliestRenewal]);
        if (!empty($candidates)) {
            $anchorStr = min($candidates);
            try { return Carbon::parse($anchorStr)->startOfDay(); } catch (\Throwable $e) {}
        }

        return now()->startOfDay();
    }

    private function computeFiveYearPeriod(Carbon $anchor, Carbon $ref): array
    {
        $start = $anchor->copy()->startOfDay();
        $ref   = $ref->copy()->startOfDay();

        if ($ref->lt($start)) {
            $periodStart = $start;
        } else {
            $years  = $start->diffInYears($ref);
            $blocks = intdiv($years, 5);
            $periodStart = $start->copy()->addYears($blocks * 5);
            while ($ref->gte($periodStart->copy()->addYears(5))) {
                $periodStart->addYears(5);
            }
        }

        $periodEnd = $periodStart->copy()->addYears(5);
        return [$periodStart, $periodEnd];
    }
}
