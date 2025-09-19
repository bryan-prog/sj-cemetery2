<?php

namespace App\Http\Controllers;

use App\Models\{ Slot, Exhumation, Reservation, GraveCell, ActionLog };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ExhumationPermitController extends Controller
{
    public function locationLabel(?Slot $slot): ?string
    {
        if (! $slot || ! $slot->cell || ! $slot->cell->level) return null;

        $cell  = $slot->cell;
        $level = $cell->level;
        $site  = $level->apartment;

        return sprintf('%s • L%s R%s C%s S%s',
            $site?->name, $level->level_no, $cell->row_no, $cell->col_no, $slot->slot_no
        );
    }

    private function releaseCellIfNoActiveLocks(int $cellId): void
    {

        $hasNonAvailable = \App\Models\Slot::where('grave_cell_id', $cellId)
            ->whereIn('status', ['reserved','occupied','renewal_pending','exhumation_pending'])
            ->exists();

        if (! $hasNonAvailable) {
            \App\Models\GraveCell::where('id', $cellId)->update(['family_id' => null]);
        }
    }

    public function listRequests(Request $request)
    {
        $raw = $request->query('status', 'pending');

        $allowed = ['pending','exhumed','all'];
        $status  = in_array($raw, $allowed, true) ? $raw : 'pending';

        $query = Exhumation::with('toSlot.cell.level.apartment', 'fromSlot.cell.level.apartment', 'reservation.deceased')
            ->latest('id');

        switch ($status) {
            case 'pending':
                $query->where('status', 'pending');
                break;

            case 'exhumed':
                $query->whereIn('status', ['approved','exhumed']);
                break;

            case 'all':
                $query->whereIn('status', ['pending','approved','exhumed']);
                break;
        }

        $exhumations = $query->get();

        return view('exhumations.requests', compact('exhumations', 'status'));
    }

    public function show(Exhumation $exhumation)
    {
        $exhumation->load(['fromSlot.cell.level.apartment', 'toSlot.cell.level.apartment', 'reservation.deceased']);

        $dec = optional($exhumation->reservation)->deceased;
        $deceasedName = $dec
            ? trim(sprintf(
                '%s%s%s%s',
                $dec->last_name ? strtoupper($dec->last_name) : '',
                $dec->first_name ? (', '.strtoupper($dec->first_name)) : '',
                $dec->middle_name ? (' '.strtoupper($dec->middle_name)) : '',
                $dec->suffix ? (' '.strtoupper($dec->suffix)) : ''
            ))
            : '—';

        $dod = $dec?->date_of_death ? Carbon::parse($dec->date_of_death)->format('Y-m-d') : null;

        return response()->json([
            'id'                         => $exhumation->id,
            'status'                     => $exhumation->status,
            'requesting_party'           => $exhumation->requesting_party,
            'address'                    => $exhumation->address,
            'contact'                    => $exhumation->contact,
            'relationship_to_deceased'   => $exhumation->relationship_to_deceased,
            'amount_as_per_ord'          => $exhumation->amount_as_per_ord,
            'date_applied'               => optional($exhumation->date_applied)->format('Y-m-d'),
            'current_location'           => $exhumation->current_location,
            'or_number'                  => $exhumation->or_number,
            'or_issued_at'               => optional($exhumation->or_issued_at)->format('Y-m-d'),

            'deceased_name'              => $deceasedName,
            'date_of_death'              => $dod,
            'from_label'                 => $this->locationLabel($exhumation->fromSlot) ?? '—',
            'to_label'                   => $this->locationLabel($exhumation->toSlot)   ?? '—',
        ]);
    }

    public function update(Request $r, Exhumation $exhumation)
    {

        abort_if(!in_array($exhumation->status, ['pending','approved','exhumed'], true), 400, 'Request already finalized.');

        $data = $r->validate([
            'requesting_party'         => 'required|string|max:255',
            'address'                  => 'nullable|string|max:255',
            'contact'                  => 'nullable|string|max:50',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'amount_as_per_ord'        => 'nullable|numeric|min:0',
            'date_applied'             => 'required|date_format:Y-m-d',
            'current_location'         => 'nullable|string|max:120',

        ]);


        $data['requesting_party'] = strtoupper($data['requesting_party']);
        if (isset($data['relationship_to_deceased'])) {
            $data['relationship_to_deceased'] = strtoupper($data['relationship_to_deceased']);
        }

        $exhumation->update($data);


        $forTransfer = $exhumation->to_slot_id
            ? ($this->locationLabel($exhumation->toSlot) ?? '—')
            : ($exhumation->current_location ?? '—');

        return response()->json([
            'message' => 'Exhumation updated.',
            'payload' => [
                'requesting_party'         => $exhumation->requesting_party,
                'relationship_to_deceased' => $exhumation->relationship_to_deceased ?? '—',
                'date_applied'             => optional($exhumation->date_applied)->format('Y-m-d'),
                'for_transfer'             => $forTransfer,
            ],
        ]);
    }

    public function store(Request $r)
    {
        $r->merge(['to_slot_id' => $r->filled('to_slot_id') ? $r->input('to_slot_id') : null]);

        $v = $r->validate([
            'reservation_id'           => 'required|exists:reservations,id',
            'from_slot_id'             => 'required|exists:slots,id',
            'to_slot_id'               => 'nullable|different:from_slot_id|exists:slots,id',
            'current_location'         => 'nullable|string|max:120',
            'date_applied'             => 'required|date_format:Y-m-d',
            'requesting_party'         => 'required|string|max:255',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'contact'                  => 'nullable|string|max:50',
            'address'                  => 'nullable|string|max:255',
            'amount_as_per_ord'        => 'nullable|numeric|min:0',
            'verifiers_id'             => 'nullable|exists:verifiers,id',
            'remarks'                  => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($v, $r) {
            /** @var \App\Models\Slot $from */
            $from = Slot::with('cell')->lockForUpdate()->find($v['from_slot_id']);
            if (! in_array($from->status, ['reserved', 'occupied'])) {
                throw ValidationException::withMessages([
                    'from_slot_id' => ['Selected slot can no longer be exhumed.'],
                ]);
            }

            $fromCellId = $from->grave_cell_id;

            $activeSourceSlots = Slot::with(['reservation'])
                ->where('grave_cell_id', $fromCellId)
                ->whereIn('status', ['reserved', 'occupied'])
                ->lockForUpdate()
                ->get()
                ->filter(fn($s) => $s->reservation);

            $hasPending = Exhumation::whereIn('from_slot_id', $activeSourceSlots->pluck('id'))
                ->where('status', 'pending')
                ->exists();

            if ($hasPending) {
                throw ValidationException::withMessages([
                    'from_slot_id' => ['A pending exhumation request already exists for a slot in this cell.'],
                ]);
            }

            $isBulk = $activeSourceSlots->count() > 1;

            if (! $isBulk) {
                $to   = null;
                $res  = Reservation::lockForUpdate()->find($v['reservation_id']);
                $sourceFamilyId = $from->cell?->family_id ?: ($res?->family_id);

                if ($v['to_slot_id']) {
                    $to = Slot::with('cell')->lockForUpdate()->find($v['to_slot_id']);

                    if ($to->status !== 'available') {
                        throw ValidationException::withMessages([
                            'to_slot_id' => ['Destination slot is not available.'],
                        ]);
                    }

                    $destCell = $to->cell;

                    if (! is_null($destCell?->family_id) && (int) $destCell->family_id !== (int) $sourceFamilyId) {
                        throw ValidationException::withMessages([
                            'to_slot_id' => ['Destination cell is reserved for a different family. Pick an empty cell or one owned by the same family.'],
                        ]);
                    }

                    if ($destCell && is_null($destCell->family_id) && $sourceFamilyId) {
                        $destCell->update(['family_id' => $sourceFamilyId]);
                    }

                    $to->update(['status' => 'exhumation_pending']);
                }

                $from->update(['status' => 'exhumation_pending']);

                $currentLoc = $r->filled('current_location') ? $r->input('current_location') : null;

                Exhumation::create([
                    'reservation_id'           => $v['reservation_id'],
                    'from_slot_id'             => $from->id,
                    'to_slot_id'               => $to?->id,
                    'current_location'         => $currentLoc,
                    'date_applied'             => $v['date_applied'],
                    'requesting_party'         => strtoupper($r->input('requesting_party')),
                    'relationship_to_deceased' => strtoupper($r->input('relationship_to_deceased','')),
                    'contact'                  => $r->input('contact') ?: null,
                    'address'                  => $r->input('address') ?: null,
                    'amount_as_per_ord'        => $v['amount_as_per_ord'],
                    'verifiers_id'             => $v['verifiers_id'],
                    'status'                   => 'pending',
                    'remarks'                  => $r->input('remarks') ?: null,
                ]);

                return;
            }

            $firstRes        = $activeSourceSlots->first()->reservation;
            $sourceFamilyId  = $from->cell->family_id ?: ($firstRes?->family_id);

            $mismatch = $activeSourceSlots->first(fn($s) =>
                (int)($s->reservation?->family_id) !== (int)$sourceFamilyId
            );
            if ($mismatch) {
                throw ValidationException::withMessages([
                    'from_slot_id' => ['All occupants in the source cell must belong to the same family to transfer as a group.'],
                ]);
            }

            if ($v['to_slot_id']) {
                $seedDest  = Slot::with('cell')->lockForUpdate()->find($v['to_slot_id']);
                $destCell  = GraveCell::with('slots')->lockForUpdate()->find($seedDest->grave_cell_id);

                if ($destCell->id === $fromCellId) {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Destination cell must be different from the source cell.'],
                    ]);
                }

                if (! is_null($destCell->family_id) && (int)$destCell->family_id !== (int)$sourceFamilyId) {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Destination cell is reserved for a different family. Pick an empty cell or a cell owned by the same family.'],
                    ]);
                }

                $availableDest = $destCell->slots->filter(fn($s) => $s->status === 'available')
                    ->sortBy('slot_no')
                    ->values();

                if ($availableDest->count() < $activeSourceSlots->count()) {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Not enough available slots in that cell to move all occupants.'],
                    ]);
                }

                $pairs = $activeSourceSlots->sortBy('slot_no')->values()
                    ->map(function ($src, $i) use ($availableDest) {
                        return [$src, $availableDest[$i]];
                    });

                foreach ($pairs as [$src, $dst]) {
                    $src->update(['status' => 'exhumation_pending']);
                    $dst->update(['status' => 'exhumation_pending']);

                    Exhumation::create([
                        'reservation_id'           => $src->reservation->id,
                        'from_slot_id'             => $src->id,
                        'to_slot_id'               => $dst->id,

                        'current_location'         => null,
                        'date_applied'             => $v['date_applied'],
                        'requesting_party'         => strtoupper($r->input('requesting_party')),
                        'relationship_to_deceased' => strtoupper($r->input('relationship_to_deceased','')),
                        'contact'                  => $r->input('contact') ?: null,
                        'address'                  => $r->input('address') ?: null,
                        'amount_as_per_ord'        => $v['amount_as_per_ord'],
                        'verifiers_id'             => $v['verifiers_id'],
                        'status'                   => 'pending',
                        'remarks'                  => trim(($r->input('remarks') ?: '') . ' [BULK CELL TRANSFER]'),
                    ]);
                }

                if (is_null($destCell->family_id) && $sourceFamilyId) {
                    $destCell->update(['family_id' => $sourceFamilyId]);
                }

                return;
            }

            $outsideLabel = $r->input('current_location');
            foreach ($activeSourceSlots as $src) {
                $src->update(['status' => 'exhumation_pending']);

                Exhumation::create([
                    'reservation_id'           => $src->reservation->id,
                    'from_slot_id'             => $src->id,
                    'to_slot_id'               => null,
                    'current_location'         => $outsideLabel,
                    'date_applied'             => $v['date_applied'],
                    'requesting_party'         => strtoupper($r->input('requesting_party')),
                    'relationship_to_deceased' => strtoupper($r->input('relationship_to_deceased','')),
                    'contact'                  => $r->input('contact') ?: null,
                    'address'                  => $r->input('address') ?: null,
                    'amount_as_per_ord'        => $v['amount_as_per_ord'],
                    'verifiers_id'             => $v['verifiers_id'],
                    'status'                   => 'pending',
                    'remarks'                  => trim(($r->input('remarks') ?: '') . ' [BULK OUTSIDE]'),
                ]);
            }
        });

        return back()->with('success', 'Exhumation request lodged! (Bulk where applicable)');
    }

    public function deny(Exhumation $exhumation)
    {
        abort_if($exhumation->status !== 'pending', 400, 'Request already processed.');

        DB::transaction(function () use ($exhumation) {

            \App\Models\Slot::lockForUpdate()
                ->where('id', $exhumation->from_slot_id)
                ->update(['status' => 'occupied']);

            if ($exhumation->to_slot_id) {
                $to = \App\Models\Slot::with('cell')->lockForUpdate()->find($exhumation->to_slot_id);
                $to->update(['status' => 'available', 'occupancy_start' => null, 'occupancy_end' => null]);

                if ($to->cell) {
                    $this->releaseCellIfNoActiveLocks($to->cell->id);
                }
            }

            $exhumation->update([
                'status'  => 'denied',
                'remarks' => 'Denied ' . \Carbon\Carbon::now()->toDateTimeString(),
            ]);


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'exhumation.denied',
                'target_type' => Exhumation::class,
                'target_id'   => $exhumation->id,
                'happened_at' => now(),
                'details'     => [
                    'from_slot' => $exhumation->from_slot_id,
                    'to_slot'   => $exhumation->to_slot_id,
                    'remarks'   => $exhumation->remarks,
                ],
            ]);
        });

        return back()->with('success', 'Exhumation request denied.');
    }

    public function approve(Request $request, Exhumation $exhumation)
    {
        abort_if($exhumation->status !== 'pending', 400, 'Request already processed.');

        $data = $request->validate([
            'or_number'    => 'required|string|max:50',
            'or_issued_at' => 'required|date',
        ]);

        DB::transaction(function () use ($exhumation, $data) {
            $this->approveOne($exhumation);

            $exhumation->update(array_merge($data, [
                'status'  => 'approved',
                'remarks' => 'Approved ' . Carbon::now()->toDateTimeString(),
            ]));


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            $fromLabel = $this->locationLabel($exhumation->fromSlot);
            $toLabel   = $exhumation->to_slot_id ? $this->locationLabel($exhumation->toSlot) : ($exhumation->current_location ?? null);

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'exhumation.approved',
                'target_type' => Exhumation::class,
                'target_id'   => $exhumation->id,
                'happened_at' => Carbon::parse($data['or_issued_at'])->startOfDay(),
                'details'     => [
                    'or_number'   => $data['or_number'],
                    'from_label'  => $fromLabel,
                    'to_label'    => $toLabel,
                ],
            ]);
        });

        return back()->with('success', 'Exhumation request approved.');
    }

    private function approveOne(Exhumation $exhumation): void
    {
        $from = Slot::with('cell')->lockForUpdate()->find($exhumation->from_slot_id);
        $from->update([
            'status'        => 'available',
            'occupancy_end' => \Carbon\Carbon::now(),
        ]);


        $res = \App\Models\Reservation::lockForUpdate()->find($exhumation->reservation_id);

        if ($exhumation->to_slot_id) {

            $to = Slot::with('cell.level')->lockForUpdate()->find($exhumation->to_slot_id);
            $to->update([
                'status'          => 'occupied',
                'occupancy_start' => \Carbon\Carbon::now(),
            ]);

            $targetLevel   = optional($to->cell)->level;
            $targetLevelId = $targetLevel?->id;
            $targetSiteId  = $targetLevel?->burial_site_id;


            if ($to->cell && is_null($to->cell->family_id)) {
                $familyId = $res?->family_id ?: ($from->cell?->family_id);
                if ($familyId) {
                    $to->cell->update(['family_id' => $familyId]);
                }
            }

            if ($res) {

                $res->update([
                    'slot_id'          => $to->id,
                    'level_id'         => $targetLevelId,
                    'burial_site_id'   => $targetSiteId,

                    'internment_sched' => \Carbon\Carbon::now(),
                ]);

                // If you prefer a date-only refresh (midnight), use:
                // 'internment_sched' => \Carbon\Carbon::now()->startOfDay(),
            }

        } else {

            if ($res) {
                $res->update([
                    'slot_id'        => null,
                    'level_id'       => null,
                    'burial_site_id' => null,

                ]);
            }
        }

        $this->releaseCellIfNoActiveBurials($from->grave_cell_id);
    }

    private function releaseCellIfNoActiveBurials(int $cellId): void
    {
        $cell = GraveCell::lockForUpdate()->find($cellId);
        if (! $cell) return;

        $hasActive = Reservation::active()
            ->whereHas('slot', fn($q) => $q->where('grave_cell_id', $cell->id))
            ->exists();

        if (! $hasActive) {
            $cell->update(['family_id' => null]);
        }
    }

    public function approveBatch(Request $request, Exhumation $exhumation)
    {
        $data = $request->validate([
            'or_number'    => 'required|string|max:50',
            'or_issued_at' => 'required|date',
        ]);

        $fromSlot = \App\Models\Slot::with('cell')->findOrFail($exhumation->from_slot_id);
        $sourceCellId = $fromSlot->grave_cell_id;

        $batch = Exhumation::query()
            ->where('status', 'pending')
            ->whereIn('from_slot_id', function ($q) use ($sourceCellId) {
                $q->select('id')->from('slots')->where('grave_cell_id', $sourceCellId);
            })
            ->orderBy('id')
            ->get();

        if ($batch->isEmpty()) {
            return back()->with('success', 'No pending exhumations to approve for this cell.');
        }

        DB::transaction(function () use ($batch, $data, $exhumation) {
            foreach ($batch as $ex) {
                $this->approveOne($ex);

                $ex->update(array_merge($data, [
                    'status'  => 'approved',
                    'remarks' => trim(($ex->remarks ?: '') . ' Approved ' . Carbon::now()->toDateTimeString()),
                ]));
            }


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'exhumation.approved_batch',
                'target_type' => Exhumation::class,
                'target_id'   => $exhumation->id,
                'happened_at' => Carbon::parse($data['or_issued_at'])->startOfDay(),
                'details'     => [
                    'batch'        => true,
                    'count'        => $batch->count(),
                    'exhumation_ids' => $batch->pluck('id')->values(),
                    'or_number'    => $data['or_number'],
                ],
            ]);
        });

        return back()->with('success', 'Approved ' . $batch->count() . ' exhumation(s) for this cell.');
    }


    public function denyBatch(Request $request, Exhumation $exhumation)
    {

        $fromSlot = \App\Models\Slot::with('cell')->findOrFail($exhumation->from_slot_id);
        $sourceCellId = $fromSlot->grave_cell_id;

        $batch = Exhumation::query()
            ->where('status', 'pending')
            ->whereIn('from_slot_id', function ($q) use ($sourceCellId) {
                $q->select('id')->from('slots')->where('grave_cell_id', $sourceCellId);
            })
            ->orderBy('id')
            ->get();

        if ($batch->isEmpty()) {
            return back()->with('success', 'No pending exhumations to deny for this cell.');
        }

        DB::transaction(function () use ($batch, $exhumation) {
            foreach ($batch as $ex) {

                $from = \App\Models\Slot::with('cell')->lockForUpdate()->find($ex->from_slot_id);
                if ($from) {
                    $from->update(['status' => 'occupied']);
                }

                if ($ex->to_slot_id) {
                    $to = \App\Models\Slot::with('cell')->lockForUpdate()->find($ex->to_slot_id);
                    if ($to) {
                        $to->update([
                            'status'          => 'available',
                            'occupancy_start' => null,
                            'occupancy_end'   => null,
                        ]);

                        if ($to->cell) {
                            $this->releaseCellIfNoActiveLocks($to->cell->id);
                        }
                    }
                }

                $ex->update([
                    'status'  => 'denied',
                    'remarks' => trim(($ex->remarks ?: '') . ' Denied ' . \Carbon\Carbon::now()->toDateTimeString()),
                ]);
            }


            $user = auth()->user();
            $username = $user?->username ?? trim(($user->fname ?? '').' '.($user->lname ?? '')) ?: null;

            ActionLog::create([
                'user_id'     => $user?->id,
                'username'    => $username,
                'action'      => 'exhumation.denied_batch',
                'target_type' => Exhumation::class,
                'target_id'   => $exhumation->id,
                'happened_at' => now(),
                'details'     => [
                    'batch'           => true,
                    'count'           => $batch->count(),
                    'exhumation_ids'  => $batch->pluck('id')->values(),
                ],
            ]);
        });

        return back()->with('success', 'Denied ' . $batch->count() . ' exhumation(s) for this cell.');
    }
}
