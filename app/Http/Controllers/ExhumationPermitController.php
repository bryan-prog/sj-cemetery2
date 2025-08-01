<?php

namespace App\Http\Controllers;

use App\Models\{ Slot, Exhumation, Reservation };
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class ExhumationPermitController extends Controller
{

    public function locationLabel(?Slot $slot): ?string
    {
        if (! $slot || ! $slot->cell || ! $slot->cell->level) {
            return null;
        }

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


    public function listRequests()
    {
        $exhumations = Exhumation::with('toSlot.cell.level.apartment', 'reservation.deceased')
                          ->latest('id')
                          ->paginate(25);

        return view('exhumations.requests', compact('exhumations'));
    }


    public function store(Request $r)
    {

        $r->merge([
            'to_slot_id' => $r->filled('to_slot_id') ? $r->input('to_slot_id') : null,
        ]);

        $v = $r->validate([
            'reservation_id'   => 'required|exists:reservations,id',
            'from_slot_id'     => 'required|exists:slots,id',

            'to_slot_id'       => 'nullable|different:from_slot_id|exists:slots,id',
            'current_location' => 'nullable|string|max:120',

            'date_applied'     => 'required|date_format:Y-m-d',
            'requesting_party' => 'required|string|max:255',
            'relationship_to_deceased' => 'nullable|string|max:100',
            'contact'          => 'nullable|string|max:50',
            'address'          => 'nullable|string|max:255',
            'amount_as_per_ord'=> 'nullable|numeric|min:0',
            'verifiers_id'     => 'nullable|exists:verifiers,id',
            'remarks'          => 'nullable|string|max:500',
        ]);


        if (Exhumation::where('from_slot_id', $v['from_slot_id'])
                      ->where('status', 'pending')
                      ->exists()) {
            return back()->withErrors([
                'from_slot_id' => 'A pending exhumation request for this slot already exists.'
            ])->withInput();
        }

        DB::transaction(function () use ($v, $r) {


            $from = Slot::lockForUpdate()->find($v['from_slot_id']);
            if (! in_array($from->status, ['reserved', 'occupied'])) {
                throw ValidationException::withMessages([
                    'from_slot_id' => ['Selected slot can no longer be exhumed.'],
                ]);
            }


            $to = null;
            if ($v['to_slot_id']) {
                $to = Slot::lockForUpdate()->find($v['to_slot_id']);
                if ($to->status !== 'available') {
                    throw ValidationException::withMessages([
                        'to_slot_id' => ['Destination slot is not available.'],
                    ]);
                }
                $to->update(['status' => 'exhumation_pending']);
            }


            $from->update(['status' => 'exhumation_pending']);


            $currentLoc = $r->input('current_location')
                         ?: ($v['to_slot_id'] ? $this->locationLabel($to) : null);

            Exhumation::create([
                'reservation_id'           => $v['reservation_id'],
                'from_slot_id'             => $from->id,
                'to_slot_id'               => $to?->id,
                'current_location'         => $currentLoc,

                'date_applied'             => $v['date_applied'],
                'requesting_party'         => strtoupper($v['requesting_party']),
                'relationship_to_deceased' => strtoupper($v['relationship_to_deceased'] ?? ''),
                'contact'                  => $v['contact'] ?? null,
                'address'                  => $v['address'] ?? null,
                'amount_as_per_ord'        => $v['amount_as_per_ord'],
                'verifiers_id'             => $v['verifiers_id'],
                'status'                   => 'pending',
                'remarks'                  => $v['remarks'] ?? null,
            ]);
        });

        return back()->with('success', 'Exhumation request lodged!');
    }


    public function deny(Exhumation $exhumation)
    {
        abort_if($exhumation->status !== 'pending', 400, 'Request already processed.');

        DB::transaction(function () use ($exhumation) {

            Slot::lockForUpdate()
                ->where('id', $exhumation->from_slot_id)
                ->update(['status' => 'occupied']);

            if ($exhumation->to_slot_id) {
                Slot::lockForUpdate()
                    ->where('id', $exhumation->to_slot_id)
                    ->update(['status' => 'available', 'occupancy_start' => null]);
            }

            $exhumation->update([
                'status'  => 'denied',
                'remarks' => 'Denied ' . Carbon::now()->toDateTimeString(),
            ]);
        });

        return back()->with('success', 'Exhumation request denied.');
    }


    public function approve(Exhumation $exhumation)
    {
        abort_if($exhumation->status !== 'pending', 400, 'Request already processed.');

        DB::transaction(function () use ($exhumation) {
            $this->approveOne($exhumation);

            $exhumation->update([
                'status'  => 'approved',
                'remarks' => 'Approved ' . Carbon::now()->toDateTimeString(),
            ]);
        });

        return back()->with('success', 'Exhumation request approved.');
    }


    private function approveOne(Exhumation $exhumation): void
    {
        $from = Slot::lockForUpdate()->find($exhumation->from_slot_id);
        $from->update([
            'status'        => 'available',
            'occupancy_end' => Carbon::now(),
        ]);

        if ($exhumation->to_slot_id) {
            $to = Slot::lockForUpdate()->find($exhumation->to_slot_id);
            $to->update([
                'status'          => 'occupied',
                'occupancy_start' => Carbon::now(),
            ]);

            Reservation::lockForUpdate()
                ->find($exhumation->reservation_id)
                ->update(['slot_id' => $to->id]);
        }
    }
}
