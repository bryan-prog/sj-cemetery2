<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [

            'id'           => $this->id,
            'created_at'   => $this->created_at?->toDateTimeString(),
            'updated_at'   => $this->updated_at?->toDateTimeString(),


            'level_id'         => $this->level_id,
            'burial_site_id'   => $this->burial_site_id,
            'grave_diggers_id' => $this->grave_diggers_id,
            'verifiers_id'     => $this->verifiers_id,
            'slot_id'          => $this->slot_id,
            'deceased_id'      => $this->deceased_id,


            'date_applied'            => $this->date_applied,
            'applicant_name'          => $this->applicant_name,
            'applicant_address'       => $this->applicant_address,
            'applicant_contact_no'    => $this->applicant_contact_no,
            'relationship_to_deceased'=> $this->relationship_to_deceased,
            'amount_as_per_ord'       => $this->amount_as_per_ord,
            'funeral_service'         => $this->funeral_service,
            'renewal_date'            => $this->renewal_date,
            'other_info'              => $this->other_info,
            'internment_sched'        => $this->internment_sched,


            'renewal_start' => $this->renewal_start,
            'renewal_end'   => $this->renewal_end,
            'buried_at'     => $this->buried_at,


            'deceased' => $this->whenLoaded('deceased', fn () => [
                'id'              => $this->deceased->id,
                'name_of_deceased'=> $this->deceased->name_of_deceased,
                'sex'             => $this->deceased->sex,
                'date_of_birth'   => $this->deceased->date_of_birth,
                'date_of_death'   => $this->deceased->date_of_death,
            ]),

            'level' => $this->whenLoaded('level', fn () => [
                'id'        => $this->level->id,
                'level_no'  => $this->level->level_no,
                'apartment' => $this->level->apartment?->only(['id','name']),
            ]),

            'burial_site' => $this->whenLoaded('burial_sites', fn () => [
                'id'   => $this->burial_sites->id,
                'name' => $this->burial_sites->name,
            ]),

            'slot' => $this->whenLoaded('slot', fn () => [
                'id'            => $this->slot->id,
                'slot_no'       => $this->slot->slot_no,
                'display_status'=> $this->slot->display_status,
            ]),

            'grave_digger' => $this->whenLoaded('grave_diggers', fn () => [
                'id'   => $this->grave_diggers->id,
                'name' => $this->grave_diggers->name,
            ]),

            'verifier' => $this->whenLoaded('verifiers', fn () => [
                'id'   => $this->verifiers->id,
                'name' => $this->verifiers->name,
            ]),
        ];
    }
}
