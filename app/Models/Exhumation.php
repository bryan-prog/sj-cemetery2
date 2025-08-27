<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exhumation extends Model
{
    protected $fillable = [
        'reservation_id',
        'from_slot_id', 'to_slot_id',
        'current_location',
        'date_applied',
        'requesting_party',
        'relationship_to_deceased',
        'contact',
        'address',
        'amount_as_per_ord',
         'or_number',
        'or_issued_at',
        'verifiers_id',
        'status',
        'remarks',

    ];

    protected $casts = [
        'date_applied' => 'date',
         'or_issued_at' => 'date',

        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
    ];


    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function fromSlot()    { return $this->belongsTo(Slot::class, 'from_slot_id'); }
    public function toSlot()      { return $this->belongsTo(Slot::class, 'to_slot_id'); }
    public function verifier()    { return $this->belongsTo(Verifier::class, 'verifiers_id'); }


    public function getDestinationLabelAttribute(): string
    {

        $makeLabel = function (?Slot $s): ?string {
            if (! $s || ! $s->cell || ! $s->cell->level) return null;
            $cell  = $s->cell;
            $level = $cell->level;
            $site  = $level->apartment;
            return sprintf(
                '%s • Level %s R%s C%s S%s',
                $site?->name,
                $level->level_no,
                $cell->row_no,
                $cell->col_no,
                $s->slot_no
            );
        };


        if ($this->to_slot_id) {
            $slot = $this->relationLoaded('toSlot')
                ? $this->toSlot
                : $this->toSlot()->with('cell.level.apartment')->first();
            if ($label = $makeLabel($slot)) return $label;
        }


        return $this->current_location ?: 'N/A';
    }
}
