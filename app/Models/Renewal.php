<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Renewal extends Model
{
    protected $fillable = [
        'reservation_id','slot_id',
        'date_applied','renewal_start','renewal_end',
        'applicant_address','contact','requesting_party','relationship_to_deceased',
        'amount_as_per_ord','verifiers_id','remarks',
        'or_number','or_issued_at','status',
    ];

    protected $casts = [
        'date_applied'  => 'date',
        'renewal_start' => 'date',
        'renewal_end'   => 'date',
        'or_issued_at'  => 'date',
    ];

    public function reservation() { return $this->belongsTo(Reservation::class); }
    public function slot()        { return $this->belongsTo(Slot::class); }
    public function verifier()    { return $this->belongsTo(Verifier::class,'verifiers_id'); }

    public function deceased()
    {
        return $this->hasOneThrough(
            Deceased::class,
            Reservation::class,
            'id',
            'id',
            'reservation_id',
            'deceased_id'
        );
    }

    protected $with    = ['deceased', 'slot.cell.level.apartment'];
    protected $appends = ['deceased_attrs', 'buried_at'];

    public function getDeceasedAttrsAttribute()
    {
        if (!$this->deceased) return null;

        return [
            'first_name'    => $this->deceased->first_name,
            'middle_name'   => $this->deceased->middle_name,
            'last_name'     => $this->deceased->last_name,
            'suffix'        => $this->deceased->suffix,
            'full_name'     => $this->deceased->full_name,
            'sex'           => $this->deceased->sex,
            'date_of_birth' => $this->deceased->date_of_birth,
            'date_of_death' => $this->deceased->date_of_death,
        ];
    }

    public function getBuriedAtAttribute()
    {
        $slot = $this->slot;
        if (!$slot) return null;

        $cell      = $slot->cell;
        $level     = $cell?->level;
        $apartment = $level?->apartment;

        return $apartment
            ? $apartment->name
              .' Level '.$level->level_no
              .' R'.$cell->row_no
              .' C'.$cell->col_no
              .' S'.$slot->slot_no
            : null;
    }
}
