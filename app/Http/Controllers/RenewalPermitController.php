<?php

namespace App\Http\Controllers;

use App\Models\{ Renewal, Slot, Reservation, ActionLog };
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

        $cellId = (int) optional(optional($renewal->slot)->cell)->id;
        $pen = $this->computePenaltyForCell(
            $cellId,
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


            'apply_to_cell'            => 'sometimes|boolean',
        ]);

        $slot   = Slot::with('cell')->findOrFail($renewal->slot_id);
        $cellId = (int) optional($slot)->grave_cell_id;

        [$pStart, $pEnd] = $this->resolvePeriod($data, $cellId, Carbon::parse($data['date_applied'])->startOfDay());


        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        if ($renewal->status === 'pending') {
            $dup = Renewal::whereIn('slot_id', $slotIds)
                ->where('id', '!=', $renewal->id)
                ->where('status', 'approved')
                ->whereDate('renewal_start', $pStart->toDateString())
                ->whereDate('renewal_end',   $pEnd->toDateString())
                ->exists();
        } else {
            $dup = Renewal::whereIn('slot_id', $slotIds)
                ->where('id','!=',$renewal->id)
                ->whereIn('status', ['pending','approved'])
                ->whereDate('renewal_start', $pStart->toDateString())
                ->whereDate('renewal_end',   $pEnd->toDateString())
                ->exists();
        }

        if ($dup) {
            throw ValidationException::withMessages([
                'renewal_start' => ["A renewal for this cell and period ({$pStart->format('Y-m-d')} to {$pEnd->format('Y-m-d')}) already exists and is approved."],
            ]);
        }

        $pen = $this->computePenaltyForCell($cellId, $pStart, $renewal->id);

        $data['renewal_start'] = $pStart->toDateString();
        $data['renewal_end']   = $pEnd->toDateString();

        // Replace any existing Penalty line
        $existingRemarks  = trim((string)($data['remarks'] ?? $renewal->remarks ?? ''));
        $withoutPenalty   = preg_replace('/\s*Penalty:\s*\d+\s*year\(s\)\s*×.*$/i', '', $existingRemarks);
        $penLine          = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
        $data['remarks']  = trim($withoutPenalty.' '.$penLine);

        $applyToCell  = $r->boolean('apply_to_cell', true);
        $updatedCount = 0;

        DB::transaction(function () use ($applyToCell, $slotIds, $renewal, $data, $cellId, $pStart, &$updatedCount) {
            // Update the row being edited
            $renewal->update($data);
            $updatedCount++;

            if (!$applyToCell) return;

            // Propagate to other PENDING rows (but DO NOT overwrite relationship_to_deceased)
            $siblings = Renewal::whereIn('slot_id', $slotIds)
                ->where('id', '!=', $renewal->id)
                ->where('status', 'pending')
                ->get();

            foreach ($siblings as $sib) {
                $penSib   = $this->computePenaltyForCell($cellId, $pStart, $sib->id);
                $existing = trim((string)($data['remarks'] ?? $sib->remarks ?? ''));
                $clean    = preg_replace('/\s*Penalty:\s*\d+\s*year\(s\)\s*×.*$/i', '', $existing);
                $penLineS = "Penalty: {$penSib['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($penSib['amount'],2,'.','');

                $sib->update([
                    'requesting_party'         => $data['requesting_party'],
                    'applicant_address'        => $data['applicant_address'],
                    'contact'                  => $data['contact'],
                    // leave relationship_to_deceased as-is (per-occupant)
                    'date_applied'             => $data['date_applied'],
                    'renewal_start'            => $data['renewal_start'],
                    'renewal_end'              => $data['renewal_end'],
                    'amount_as_per_ord'        => $data['amount_as_per_ord'] ?? null,
                    'remarks'                  => trim($clean.' '.$penLineS),
                ]);
                $updatedCount++;
            }
        });

        $renewal->refresh()->load(['slot.cell.level.apartment', 'deceased']);

        $deceasedName = optional($renewal->deceased)?->last_name
            ? (strtoupper($renewal->deceased->last_name).', '.strtoupper($renewal->deceased->first_name ?? ''))
            : '—';

        return response()->json([
            'message'  => $updatedCount > 1
                ? "Renewal updated. Applied to {$updatedCount} pending item(s) in this cell."
                : 'Renewal updated.',
            'payload'  => [
                'requesting_party' => $renewal->requesting_party,
                'relationship'     => $renewal->relationship_to_deceased ?? '—',
                'deceased_name'    => $deceasedName,
                'buried_at'        => $renewal->buried_at ?? '—',
                'period'           => ($renewal->renewal_start?->format('Y') ?? '—').' – '.($renewal->renewal_end?->format('Y') ?? '—'),
                'penalty_years'    => $pen['years'],
                'penalty_amount'   => number_format($pen['amount'], 2, '.', ''),
                'updated_count'    => $updatedCount,
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


                    $payload['relationship_to_deceased'] = $res->relationship_to_deceased;

                    $pen = $this->computePenaltyForCell($cellId, Carbon::parse($v['renewal_start']));
                    $penLine = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
                    $payload['remarks'] = trim(($payload['remarks'] ?? '') . ' [BULK FAMILY CELL] ' . $penLine);

                    Renewal::create($payload);
                }

                if ($reservations->isEmpty()) {
                    Slot::lockForUpdate()->where('id', $v['slot_id'])
                        ->update(['status' => 'renewal_pending']);

                    $pen = $this->computePenaltyForCell($cellId, Carbon::parse($v['renewal_start']));
                    $penLine = "Penalty: {$pen['years']} year(s) × ".number_format(self::PENALTY_PER_YEAR,2,'.','')." = ".number_format($pen['amount'],2,'.','');
                    $v['remarks'] = trim(($v['remarks'] ?? '') . ' [BULK FAMILY CELL] ' . $penLine);

                    Renewal::create($v);
                }
            } else {
                Slot::lockForUpdate()
                    ->where('id', $v['slot_id'])
                    ->update(['status' => 'renewal_pending']);

                $pen = $this->computePenaltyForCell($cellId, Carbon::parse($v['renewal_start']));
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


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'renewal.approved',
                'target_type' => Renewal::class,
                'target_id'   => $renewal->id,
                'happened_at' => Carbon::parse($data['or_issued_at'])->startOfDay(),
                'details'     => [
                    'or_number' => $data['or_number'],
                    'period'    => [
                        'start' => optional($renewal->renewal_start)->toDateString(),
                        'end'   => optional($renewal->renewal_end)->toDateString(),
                    ],
                ],
            ]);
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


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'renewal.denied',
                'target_type' => Renewal::class,
                'target_id'   => $renewal->id,
                'happened_at' => now(),
                'details'     => [
                    'remarks' => $renewal->remarks,
                ],
            ]);
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

        DB::transaction(function () use ($batch, $data, $renewal) {
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


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'renewal.approved_batch',
                'target_type' => Renewal::class,
                'target_id'   => $renewal->id,
                'happened_at' => Carbon::parse($data['or_issued_at'])->startOfDay(),
                'details'     => [
                    'batch'       => true,
                    'count'       => $batch->count(),
                    'renewal_ids' => $batch->pluck('id')->values(),
                    'or_number'   => $data['or_number'],
                ],
            ]);
        });

        return back()->with('success', 'Approved ' . $batch->count() . ' renewal request(s) for this cell.');
    }

    public function denyBatch(Renewal $renewal)
    {
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
            return back()->with('info', 'No pending renewals to deny for this cell.');
        }

        DB::transaction(function () use ($batch, $renewal) {
            foreach ($batch as $r) {
                $r->update([
                    'status'  => 'denied',
                    'remarks' => trim(($r->remarks ?: '') . ' Denied ' . now()->toDateTimeString()),
                ]);

                if ($r->slot_id) {
                    Slot::lockForUpdate()
                        ->where('id', $r->slot_id)
                        ->update(['status' => 'occupied']);
                }
            }


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'renewal.denied_batch',
                'target_type' => Renewal::class,
                'target_id'   => $renewal->id,
                'happened_at' => now(),
                'details'     => [
                    'batch'       => true,
                    'count'       => $batch->count(),
                    'renewal_ids' => $batch->pluck('id')->values(),
                ],
            ]);
        });

        return back()->with('success', 'Denied ' . $batch->count() . ' renewal request(s) for this cell.');
    }



    public function pendingByCell(Renewal $renewal)
    {
        abort_unless($renewal->slot_id, 404);

        $slot   = Slot::with('cell.level.apartment')->findOrFail($renewal->slot_id);
        $cellId = (int) $slot->grave_cell_id;

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        $rows = Renewal::with(['deceased','slot.cell.level.apartment'])
            ->whereIn('slot_id', $slotIds)
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        $payload = $rows->map(function ($r) {
            $dec  = $r->deceased;
            $cell = $r->slot?->cell; $lvl = $cell?->level; $apt = $lvl?->apartment;
            return [
                'renewal_id'   => $r->id,
                'deceased'     => $dec?->full_name ?? ($dec?->last_name ? ($dec->last_name.', '.$dec->first_name) : '—'),
                'relationship' => $r->relationship_to_deceased,
                'location'     => $apt
                    ? ($apt->name.' • L'.$lvl->level_no.' R'.$cell->row_no.' C'.$cell->col_no.' S'.$r->slot?->slot_no)
                    : '—',
            ];
        });

        return response()->json([
            'cell_label' => ($slot->cell && $slot->cell->level && $slot->cell->level->apartment)
                ? ($slot->cell->level->apartment->name.' • L'.$slot->cell->level->level_no.' R'.$slot->cell->row_no.' C'.$slot->cell->col_no)
                : '—',
            'items' => $payload,
        ]);
    }

    public function bulkRelationships(Request $r, Renewal $renewal)
    {
        abort_unless($renewal->status === 'pending', 400, 'Only pending renewals can be batch-edited.');

        $data = $r->validate([
            'relationship_map'   => 'required|array|min:1',
            'relationship_map.*' => 'nullable|string|max:100',
        ]);

        $slot = Slot::with('cell')->findOrFail($renewal->slot_id);
        $cellId = (int) optional($slot)->grave_cell_id;

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        $pending = Renewal::whereIn('slot_id', $slotIds)
            ->where('status', 'pending')
            ->pluck('id')
            ->all();

        $updates = 0;

        DB::transaction(function () use ($data, $pending, &$updates) {
            foreach ($data['relationship_map'] as $renewalId => $rel) {
                $id = (int)$renewalId;
                if (!in_array($id, $pending, true)) continue;

                Renewal::where('id', $id)->update([
                    'relationship_to_deceased' => ($rel !== null && trim($rel) !== '') ? trim($rel) : null,
                ]);
                $updates++;
            }
        });

        return response()->json([
            'message' => "Updated relationship for {$updates} pending renewal(s).",
            'updated' => $updates,
        ]);
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

            if (!$start || !$end || $end->ne($start->copy()->addYears(5))) {
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

    private function computePenaltyForCell(?int $cellId, ?Carbon $newStart, ?int $ignoreRenewalId = null): array
    {
        if (!$cellId || !$newStart) {
            return ['years' => 0, 'amount' => 0.0];
        }

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        if ($slotIds->isEmpty()) {
            return ['years' => 0, 'amount' => 0.0];
        }

        $latestApprovedEnd = Renewal::whereIn('slot_id', $slotIds)
            ->where('status', 'approved')
            ->when($ignoreRenewalId, fn($q) => $q->where('id', '!=', $ignoreRenewalId))
            ->whereDate('renewal_end', '<=', $newStart->toDateString())
            ->max('renewal_end');

        if ($latestApprovedEnd) {
            $coverageEnd = Carbon::parse($latestApprovedEnd)->startOfDay();
        } else {
            $anchor = $this->getCellAnchorDate($cellId);
            $coverageEnd = $anchor->copy()->addYears(5);
        }

        if ($newStart->lte($coverageEnd)) {
            return ['years' => 0, 'amount' => 0.0];
        }

        $years  = $coverageEnd->diffInYears($newStart);
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
