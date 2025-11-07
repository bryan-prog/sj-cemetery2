<?php

namespace App\Http\Controllers;

use App\Models\{
    Slot,
    Exhumation,
    Reservation,
    GraveCell,
    ActionLog,
    Renewal
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Carbon\Carbon;

class ExhumationPermitController extends Controller
{
    use AuthorizesRequests;



    public function locationLabel(?Slot $slot): ?string
    {
        if (!$slot || !$slot->cell || !$slot->cell->level) return null;

        $cell  = $slot->cell;
        $level = $cell->level;
        $site  = $level->apartment;

        return sprintf(
            '%s • L%s R%s C%s S%s',
            $site?->name,
            $level->level_no,
            $cell->row_no,
            $cell->col_no,
            $slot->slot_no
        );
    }

    private function formatOutsideLocation(?string $loc, bool $forCremation): ?string
    {
        if ($loc === null || trim($loc) === '') return null;

        $label  = trim($loc);
        if ($forCremation) {
            $prefix = 'FOR CREMATION - TRANSFER LOCATION : ';

            if (substr($label, 0, strlen($prefix)) !== $prefix) {
                $label = $prefix . $label;
            }
        }
        return $label;
    }

    private function computeOrdTotal(int $n, ?float $override = null): float
    {
        if (is_numeric($override)) {
            return round((float)$override, 2);
        }

        $labor = 3000;
        $perBody = 300 + 200;
        return round($labor + ($perBody * max(1, $n)), 2);
    }


    private function splitAmount(float $total, int $n): array
    {
        $n = max(1, $n);
        $cents = (int) round($total * 100);
        $base  = intdiv($cents, $n);
        $rem   = $cents - ($base * $n);

        $shares = array_fill(0, $n, $base);
        for ($i = 0; $i < $rem; $i++) $shares[$i]++;

        return array_map(fn($c) => round($c / 100, 2), $shares);
    }

    private function releaseCellIfNoActiveLocks(int $cellId): void
    {
        $hasNonAvailable = Slot::where('grave_cell_id', $cellId)
            ->whereIn('status', ['reserved','occupied','renewal_pending','exhumation_pending'])
            ->exists();

        if (!$hasNonAvailable) {
            GraveCell::where('id', $cellId)->update(['family_id' => null]);
        }
    }

    private function purgeCellRenewals(int $cellId): void
    {
        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');
        if ($slotIds->isEmpty()) return;

        Renewal::whereIn('slot_id', $slotIds)
            ->whereIn('status', ['pending','approved'])
            ->delete();
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
            'id'                       => $exhumation->id,
            'status'                   => $exhumation->status,
            'requesting_party'         => $exhumation->requesting_party,
            'address'                  => $exhumation->address,
            'contact'                  => $exhumation->contact,
            'relationship_to_deceased' => $exhumation->relationship_to_deceased,
            'amount_as_per_ord'        => $exhumation->amount_as_per_ord,
            'date_applied'             => optional($exhumation->date_applied)->format('Y-m-d'),
            'current_location'         => $exhumation->current_location,
            'or_number'                => $exhumation->or_number,
            'or_issued_at'             => optional($exhumation->or_issued_at)->format('Y-m-d'),

            'deceased_name'            => $deceasedName,
            'date_of_death'            => $dod,
            'from_label'               => $this->locationLabel($exhumation->fromSlot) ?? '—',
            'to_label'                 => $this->locationLabel($exhumation->toSlot)   ?? '—',
            'for_cremation'            => (bool) $exhumation->for_cremation,
        ]);
    }

    public function update(Request $r, Exhumation $exhumation)
    {
        $this->authorize('edit-permits');

        abort_if(!in_array($exhumation->status, ['pending','approved','exhumed'], true), 400, 'Request already finalized.');

        $data = $r->validate([
            'requesting_party'         => 'required|string|max:255',
            'address'                  => 'nullable|string|max:255',
            'contact'                  => 'nullable|string|max:50',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'amount_as_per_ord'        => 'nullable|numeric|min:0',
            'date_applied'             => 'required|date_format:Y-m-d',
            'current_location'         => 'nullable|string|max:120',
            'for_cremation'            => 'nullable|boolean',
        ]);

        $data['requesting_party'] = strtoupper($data['requesting_party']);
        if (isset($data['relationship_to_deceased'])) {
            $data['relationship_to_deceased'] = strtoupper($data['relationship_to_deceased']);
        }


        $forCrem = (bool)($data['for_cremation'] ?? $exhumation->for_cremation);
        if (array_key_exists('current_location', $data)) {
            $data['current_location'] = $this->formatOutsideLocation($data['current_location'], $forCrem);
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
                'for_cremation'            => (bool) $exhumation->for_cremation,
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
            'for_cremation'            => 'nullable|boolean',

            'selected_from_ids'        => 'sometimes|array|min:1',
            'selected_from_ids.*'      => 'integer|exists:slots,id',

            'inside_transfer'          => 'sometimes|boolean',
        ]);


        $inside = $r->boolean('inside_transfer');
        if ($inside) {
            if (empty($v['to_slot_id'])) {
                throw ValidationException::withMessages([
                    'to_slot_id' => ['Please choose an available destination slot for inside transfer.'],
                ]);
            }
        } else {
            if (!$r->filled('current_location')) {
                throw ValidationException::withMessages([
                    'current_location' => ['Please provide the transfer location for outside/cremation.'],
                ]);
            }
        }

        DB::transaction(function () use ($v, $r) {
            /** @var \App\Models\Slot $from */
            $from = Slot::with('cell')->lockForUpdate()->find($v['from_slot_id']);
            if (!in_array($from->status, ['reserved', 'occupied'], true)) {
                throw ValidationException::withMessages([
                    'from_slot_id' => ['Selected slot can no longer be exhumed.'],
                ]);
            }

            $fromCellId = (int) $from->grave_cell_id;


            $subsetIds = collect($r->input('selected_from_ids', []))
                ->filter(fn($x) => !is_null($x))
                ->map(fn($x) => (int) $x)
                ->unique()
                ->values();

            if ($subsetIds->isNotEmpty() && !$subsetIds->contains((int)$from->id)) {
                $subsetIds->prepend((int)$from->id)->unique()->values();
            }

            if ($subsetIds->isNotEmpty()) {
                $sources = Slot::with(['reservation','cell'])->lockForUpdate()
                    ->whereIn('id', $subsetIds->all())
                    ->get();

                if ($sources->isEmpty()) {
                    throw ValidationException::withMessages([
                        'selected_from_ids' => ['No valid occupants were selected.'],
                    ]);
                }

                $bad = $sources->first(function ($s) use ($fromCellId) {
                    return (int)$s->grave_cell_id !== $fromCellId
                        || !in_array($s->status, ['reserved','occupied'], true)
                        || !$s->reservation;
                });
                if ($bad) {
                    throw ValidationException::withMessages([
                        'selected_from_ids' => ['All selected occupants must be RESERVED/OCCUPIED in the same source cell and have an active reservation.'],
                    ]);
                }

                $hasPendingAny = Exhumation::whereIn('from_slot_id', $sources->pluck('id'))
                    ->where('status', 'pending')
                    ->exists();
                if ($hasPendingAny) {
                    throw ValidationException::withMessages([
                        'selected_from_ids' => ['A pending exhumation already exists for one or more of the selected occupants.'],
                    ]);
                }
            } else {
                $sources = Slot::with(['reservation'])
                    ->where('grave_cell_id', $fromCellId)
                    ->whereIn('status', ['reserved','occupied'])
                    ->lockForUpdate()
                    ->get()
                    ->filter(fn($s) => $s->reservation);

                $hasPending = Exhumation::whereIn('from_slot_id', $sources->pluck('id'))
                    ->where('status', 'pending')
                    ->exists();
                if ($hasPending) {
                    throw ValidationException::withMessages([
                        'from_slot_id' => ['A pending exhumation request already exists for a slot in this cell.'],
                    ]);
                }
            }

            $countSources = $sources->count();
            $isBulk       = $countSources > 1;
            $forCremation = $r->boolean('for_cremation');

            $firstRes        = $sources->first()?->reservation;
            $sourceFamilyId  = $from->cell?->family_id ?: ($firstRes?->family_id);

            if ($isBulk) {
                $mismatch = $sources->first(function ($s) use ($sourceFamilyId) {
                    return (int)($s->reservation?->family_id) !== (int)$sourceFamilyId;
                });
                if ($mismatch) {
                    throw ValidationException::withMessages([
                        'from_slot_id' => ['All occupants in the selected set must belong to the same family to transfer as a group.'],
                    ]);
                }
            }


            $totalAsPerOrd = $this->computeOrdTotal($countSources, $v['amount_as_per_ord'] ?? null);
            $perRowShares  = $this->splitAmount($totalAsPerOrd, max(1, $countSources));


            $outsideFormatted = $this->formatOutsideLocation($v['current_location'] ?? null, $forCremation);


            if (!$isBulk) {
                /** @var \App\Models\Slot $src */
                $src = $sources->first();
                $to  = null;

                if ($v['to_slot_id']) {
                    $to = Slot::with('cell')->lockForUpdate()->find($v['to_slot_id']);

                    if ($to->status !== 'available') {
                        throw ValidationException::withMessages([
                            'to_slot_id' => ['Destination slot is not available.'],
                        ]);
                    }

                    $destCell = $to->cell;
                    if (!is_null($destCell?->family_id) && (int)$destCell->family_id !== (int)$sourceFamilyId) {
                        throw ValidationException::withMessages([
                            'to_slot_id' => ['Destination cell is reserved for a different family. Pick an empty cell or one owned by the same family.'],
                        ]);
                    }

                    if ($destCell && is_null($destCell->family_id) && $sourceFamilyId) {
                        $destCell->update(['family_id' => $sourceFamilyId]);
                    }

                    $to->update(['status' => 'exhumation_pending']);
                }

                $src->update(['status' => 'exhumation_pending']);

                Exhumation::create([
                    'reservation_id'           => $src->reservation->id,
                    'from_slot_id'             => $src->id,
                    'to_slot_id'               => $to?->id,
                    'current_location'         => $to ? null : $outsideFormatted,
                    'date_applied'             => $v['date_applied'],
                    'requesting_party'         => strtoupper($r->input('requesting_party')),
                    'relationship_to_deceased' => strtoupper($r->input('relationship_to_deceased','')),
                    'contact'                  => $r->input('contact') ?: null,
                    'address'                  => $r->input('address') ?: null,
                    'amount_as_per_ord'        => $perRowShares[0],
                    'verifiers_id'             => $v['verifiers_id'],
                    'status'                   => 'pending',
                    'remarks'                  => $r->input('remarks') ?: null,
                    'for_cremation'            => $forCremation,
                ]);

                return;
            }


            if ($v['to_slot_id']) {
                $seedDest  = Slot::with('cell')->lockForUpdate()->find($v['to_slot_id']);
                $destCell  = GraveCell::with('slots')->lockForUpdate()->find($seedDest->grave_cell_id);

                if ($destCell->id === $fromCellId) {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Destination cell must be different from the source cell.'],
                    ]);
                }

                if (!is_null($destCell->family_id) && (int)$destCell->family_id !== (int)$sourceFamilyId) {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Destination cell is reserved for a different family. Pick an empty cell or a cell owned by the same family.'],
                    ]);
                }

                $availableDest = $destCell->slots
                    ->filter(fn($s) => $s->status === 'available')
                    ->sortBy('slot_no')
                    ->values();

                if ($availableDest->count() < $countSources) {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Not enough available slots in that cell to move all selected occupants.'],
                    ]);
                }

                $sourcesSorted = $sources->sortBy('slot_no')->values();
                $pairs = $sourcesSorted->map(function ($src, $i) use ($availableDest) {
                    return [$src, $availableDest[$i]];
                });

                foreach ($pairs as $idx => [$src, $dst]) {
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
                        'amount_as_per_ord'        => $perRowShares[$idx],
                        'verifiers_id'             => $v['verifiers_id'],
                        'status'                   => 'pending',
                        'remarks'                  => trim(($r->input('remarks') ?: '') . ' [BULK CELL TRANSFER]'),
                        'for_cremation'            => $forCremation,
                    ]);
                }

                if (is_null($destCell->family_id) && $sourceFamilyId) {
                    $destCell->update(['family_id' => $sourceFamilyId]);
                }

                return;
            }


            foreach ($sources as $idx => $src) {
                $src->update(['status' => 'exhumation_pending']);

                Exhumation::create([
                    'reservation_id'           => $src->reservation->id,
                    'from_slot_id'             => $src->id,
                    'to_slot_id'               => null,
                    'current_location'         => $outsideFormatted,
                    'date_applied'             => $v['date_applied'],
                    'requesting_party'         => strtoupper($r->input('requesting_party')),
                    'relationship_to_deceased' => strtoupper($r->input('relationship_to_deceased','')),
                    'contact'                  => $r->input('contact') ?: null,
                    'address'                  => $r->input('address') ?: null,
                    'amount_as_per_ord'        => $perRowShares[$idx],
                    'verifiers_id'             => $v['verifiers_id'],
                    'status'                   => 'pending',
                    'remarks'                  => trim(($r->input('remarks') ?: '') . ' [BULK OUTSIDE]'),
                    'for_cremation'            => $forCremation,
                ]);
            }
        });

        return back()->with('success', 'Exhumation request lodged! (Subset/Bulk where applicable)');
    }



    public function pendingByCell(Exhumation $exhumation)
    {
        $fromSlot = Slot::with('cell.level.apartment')->findOrFail($exhumation->from_slot_id);
        $cellId   = (int) $fromSlot->grave_cell_id;

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        $rows = Exhumation::with(['reservation.deceased','fromSlot.cell.level.apartment','toSlot.cell.level.apartment'])
            ->whereIn('from_slot_id', $slotIds)
            ->where('status', 'pending')
            ->orderBy('id')
            ->get();

        $cellLabel = ($fromSlot->cell && $fromSlot->cell->level && $fromSlot->cell->level->apartment)
            ? ($fromSlot->cell->level->apartment->name.' • L'.$fromSlot->cell->level->level_no.' R'.$fromSlot->cell->row_no.' C'.$fromSlot->cell->col_no)
            : '—';

        $items = $rows->map(function ($ex) {
            $dec = optional($ex->reservation)->deceased;
            $decName = $dec?->full_name
                ?: ($dec?->last_name ? ($dec->last_name.', '.($dec->first_name ?? '')) : '—');

            $origin = $this->locationLabel($ex->fromSlot) ?? '—';

            return [
                'exhumation_id' => $ex->id,
                'deceased'      => $decName,
                'relationship'  => $ex->relationship_to_deceased,
                'origin'        => $origin,
                'for_cremation' => (bool) $ex->for_cremation,
            ];
        })->values();

        return response()->json([
            'cell_label' => $cellLabel,
            'items'      => $items,
        ]);
    }

    public function bulkRelationships(Request $r, Exhumation $exhumation)
    {
        abort_unless($exhumation->status === 'pending', 400, 'Only pending exhumations can be batch-edited.');

        $data = $r->validate([
            'relationship_map'   => 'required|array|min:1',
            'relationship_map.*' => 'nullable|string|max:100',
        ]);

        $fromSlot = Slot::with('cell')->findOrFail($exhumation->from_slot_id);
        $cellId   = (int) optional($fromSlot)->grave_cell_id;

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        $pendingIds = Exhumation::whereIn('from_slot_id', $slotIds)
            ->where('status', 'pending')
            ->pluck('id')
            ->all();

        $updates = 0;

        DB::transaction(function () use ($data, $pendingIds, &$updates) {
            foreach ($data['relationship_map'] as $exhumationId => $rel) {
                $id = (int) $exhumationId;
                if (!in_array($id, $pendingIds, true)) continue;

                $value = (isset($rel) && trim($rel) !== '') ? strtoupper(trim($rel)) : null;

                Exhumation::where('id', $id)->update([
                    'relationship_to_deceased' => $value,
                ]);
                $updates++;
            }
        });

        return response()->json([
            'message' => "Updated relationship for {$updates} pending exhumation(s).",
            'updated' => $updates,
        ]);
    }



    public function deny(Exhumation $exhumation)
    {
        $this->authorize('approve-deny');
        abort_if($exhumation->status !== 'pending', 400, 'Request already processed.');

        DB::transaction(function () use ($exhumation) {

            Slot::lockForUpdate()
                ->where('id', $exhumation->from_slot_id)
                ->update(['status' => 'occupied']);

            if ($exhumation->to_slot_id) {
                $to = Slot::with('cell')->lockForUpdate()->find($exhumation->to_slot_id);
                $to->update(['status' => 'available', 'occupancy_start' => null, 'occupancy_end' => null]);

                if ($to->cell) {
                    $this->releaseCellIfNoActiveLocks($to->cell->id);
                }
            }

            $exhumation->update([
                'status'  => 'denied',
                'remarks' => 'Denied ' . Carbon::now()->toDateTimeString(),
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
                    'from_slot'     => $exhumation->from_slot_id,
                    'to_slot'       => $exhumation->to_slot_id,
                    'remarks'       => $exhumation->remarks,
                    'for_cremation' => (bool) $exhumation->for_cremation,
                ],
            ]);
        });

        return back()->with('success', 'Exhumation request denied.');
    }

    public function approve(Request $request, Exhumation $exhumation)
    {
        $this->authorize('approve-deny');
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
            $username = $user?->username ?? trim(($user->fname ?? '').' '.(($user->lname ?? ''))) ?: null;

            $fromLabel = $this->locationLabel($exhumation->fromSlot);
            $toLabel   = $exhumation->to_slot_id
                ? $this->locationLabel($exhumation->toSlot)
                : ($exhumation->current_location ?? null);

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
            'occupancy_end' => Carbon::now(),
        ]);

        $res = Reservation::lockForUpdate()->find($exhumation->reservation_id);

        if ($exhumation->to_slot_id) {
            $to = Slot::with('cell.level')->lockForUpdate()->find($exhumation->to_slot_id);
            $to->update([
                'status'          => 'occupied',
                'occupancy_start' => Carbon::now(),
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
                    'internment_sched' => Carbon::now(),
                ]);
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

        $sourceCellId = $from->grave_cell_id;

        $hasActive = Reservation::active()
            ->whereHas('slot', fn($q) => $q->where('grave_cell_id', $sourceCellId))
            ->exists();

        if (!$hasActive) {
            $this->purgeCellRenewals($sourceCellId);
        }

        $this->releaseCellIfNoActiveBurials($sourceCellId);
    }

    private function releaseCellIfNoActiveBurials(int $cellId): void
    {
        $cell = GraveCell::lockForUpdate()->find($cellId);
        if (!$cell) return;

        $hasActive = Reservation::active()
            ->whereHas('slot', fn($q) => $q->where('grave_cell_id', $cell->id))
            ->exists();

        if (!$hasActive) {
            $cell->update(['family_id' => null]);
        }
    }

    public function approveBatch(Request $request, Exhumation $exhumation)
    {
        $this->authorize('approve-deny');

        $data = $request->validate([
            'or_number'    => 'required|string|max:50',
            'or_issued_at' => 'required|date',
        ]);

        $fromSlot = Slot::with('cell')->findOrFail($exhumation->from_slot_id);
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
                    'batch'          => true,
                    'count'          => $batch->count(),
                    'exhumation_ids' => $batch->pluck('id')->values(),
                    'or_number'      => $data['or_number'],
                ],
            ]);
        });

        return back()->with('success', 'Approved ' . $batch->count() . ' exhumation(s) for this cell.');
    }

    public function denyBatch(Request $request, Exhumation $exhumation)
    {
        $this->authorize('approve-deny');

        $fromSlot = Slot::with('cell')->findOrFail($exhumation->from_slot_id);
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
                $from = Slot::with('cell')->lockForUpdate()->find($ex->from_slot_id);
                if ($from) {
                    $from->update(['status' => 'occupied']);
                }

                if ($ex->to_slot_id) {
                    $to = Slot::with('cell')->lockForUpdate()->find($ex->to_slot_id);
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
                    'remarks' => trim(($ex->remarks ?: '') . ' Denied ' . Carbon::now()->toDateTimeString()),
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
