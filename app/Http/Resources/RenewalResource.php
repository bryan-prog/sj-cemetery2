<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RenewalResource extends JsonResource
{
    public function toArray($request)
    {
        return [

            'id'         => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),


            'reservation_id' => $this->reservation_id,
            'slot_id'        => $this->slot_id,
            'verifiers_id'   => $this->verifiers_id,
            'date_applied'            => $this->date_applied,
            'renewal_start'           => $this->renewal_start,
            'renewal_end'             => $this->renewal_end,
            'applicant_address'       => $this->applicant_address,
            'contact'                 => $this->contact,
            'requesting_party'        => $this->requesting_party,
            'relationship_to_deceased'=> $this->relationship_to_deceased,
            'amount_as_per_ord'       => $this->amount_as_per_ord,
            'remarks'                 => $this->remarks,
            'status'                  => $this->status,
            'deceased'  => $this->deceased_attrs,
            'buried_at' => optional($this->slot)->location_label,
            'verifier' => $this->whenLoaded('verifier', fn () => [
                'id'   => $this->verifier->id,
                'name' => $this->verifier->name_of_verifier,
            ]),


        ];
    }
}
