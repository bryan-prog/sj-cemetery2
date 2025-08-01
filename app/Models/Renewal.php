<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Renewal extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reservation_id','slot_id',
        'date_applied','renewal_start','renewal_end',
        'applicant_address', 'contact','requesting_party','relationship_to_deceased',
        'amount_as_per_ord','verifiers_id','remarks','status',
    ];

    protected $casts = [
        'date_applied'  => 'date',
        'renewal_start' => 'date',
        'renewal_end'   => 'date',
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


    protected $with    = ['deceased'];
    protected $appends = ['deceased_attrs'];

    public function getDeceasedAttrsAttribute()
    {
        return $this->deceased?->only([
            'name_of_deceased',
            'sex',
            'date_of_birth',
            'date_of_death',
        ]);
    }
}
