<?php

namespace App\Support;

use App\Models\{Reservation, Slot, Renewal};
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CellRenewalSync
{

    public function syncForReservation(Reservation $reservation): void
    {
        $slot = $reservation->slot()->with('cell')->first();
        if (!$slot || !$slot->grave_cell_id) {
            return;
        }

        $cellId  = (int) $slot->grave_cell_id;
        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');

        if (Renewal::where('reservation_id', $reservation->id)->exists()) {
            return;
        }


        $fallbackParty   = $this->applicantFullName($reservation) ?: 'SYSTEM AUTOTAG';
        $fallbackAddress = $reservation->applicant_address ?: '—';
        $fallbackContact = $reservation->applicant_contact_no ?: '—';
        $today           = now()->toDateString();

        DB::transaction(function () use (
            $slot, $slotIds, $reservation, $cellId,
            $fallbackParty, $fallbackAddress, $fallbackContact, $today
        ) {

            $pending = Renewal::whereIn('slot_id', $slotIds)
                ->whereRaw('LOWER(status) = ?', ['pending'])
                ->orderByDesc('id')
                ->first();

            if ($pending) {
                Renewal::create([
                    'reservation_id'           => $reservation->id,
                    'slot_id'                  => $reservation->slot_id,
                    'date_applied'             => $today,
                    'renewal_start'            => optional($pending->renewal_start)->toDateString(),
                    'renewal_end'              => optional($pending->renewal_end)->toDateString(),
                    'status'                   => 'pending',
                    'remarks'                  => trim(($pending->remarks ?? '') . ' [AUTO-COPY for new reservation]'),


                    'requesting_party'         => $pending->requesting_party ?: $fallbackParty,
                    'applicant_address'        => $pending->applicant_address ?: $fallbackAddress,
                    'contact'                  => $pending->contact ?: $fallbackContact,


                    'relationship_to_deceased' => $pending->relationship_to_deceased,
                    'amount_as_per_ord'        => $pending->amount_as_per_ord,
                    'verifiers_id'             => $pending->verifiers_id,
                ]);


                if ($slot->status !== 'renewal_pending') {
                    $slot->update(['status' => 'renewal_pending']);
                }
                return;
            }


            $coverageEnd = app(\App\Support\CellCoverage::class)->coverageEndForCell($cellId, now());
            if ($coverageEnd) {
                $latestApproved = Renewal::whereIn('slot_id', $slotIds)
                    ->whereRaw('LOWER(status) = ?', ['approved'])
                    ->orderByDesc('renewal_end')
                    ->first();


                if (!$latestApproved) {
                    return;
                }

                $start = Carbon::parse($latestApproved->renewal_start)->startOfDay();

                Renewal::create([
                    'reservation_id'      => $reservation->id,
                    'slot_id'             => $reservation->slot_id,
                    'date_applied'        => $today,
                    'renewal_start'       => $start->toDateString(),
                    'renewal_end'         => $coverageEnd->toDateString(),
                    'status'              => 'approved',
                    'remarks'             => '[AUTO-TAG] Covered by cell’s approved renewal period',

                    'requesting_party'    => $fallbackParty,
                    'applicant_address'   => $fallbackAddress,
                    'contact'             => $fallbackContact,
                ]);


                if ($slot->status === 'for_penalty') {
                    $slot->update(['status' => 'occupied']);
                }
            }
        });
    }

    private function applicantFullName(Reservation $r): ?string
    {
        $parts = array_filter([
            $r->applicant_first_name,
            $r->applicant_middle_name,
            $r->applicant_last_name,
            $r->applicant_suffix,
        ], fn ($v) => (string) $v !== '');
        $name = trim(implode(' ', $parts));
        return $name !== '' ? $name : null;
        }
}
