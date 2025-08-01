<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';


        protected $fillable = [
        'level_id',
        'deceased_id',
        'grave_diggers_id',
        'burial_site_id',
        'verifiers_id',
        'slot_id',
        'date_applied',
        'applicant_name',
        'applicant_address',
        'applicant_contact_no',
        'relationship_to_deceased',
        'amount_as_per_ord',
        'funeral_service',
        'renewal_date',
        'other_info',
        'internment_sched',
    ];


    protected $appends = [
    'renewal_start', 'renewal_end', 'buried_at',
];


    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id');
    }

    public function burial_sites(){
         return $this->belongsTo(BurialSite::class, 'burial_site_id');

    }
      public function deceased()
    {
        return $this->belongsTo(Deceased::class, 'deceased_id');

    }

    public function grave_diggers(){
        return $this->belongsTo(GraveDiggers::class, 'grave_diggers_id');
    }

    public function verifiers(){
       return $this->belongsTo(Verifier::class, 'verifiers_id');
    }

     public function slot()
    {
        return $this->belongsTo(Slot::class, 'slot_id');
    }

       public function renewals()
    {
        return $this->hasMany(Renewal::class)->oldest('renewal_start');
    }

    public function latestApprovedRenewal()
    {
        return $this->hasOne(Renewal::class)
                    ->whereRaw('LOWER(status) = ?', ['approved'])
                    ->latestOfMany();
    }

    public function getRenewalStartAttribute()
{
    return optional($this->latestApprovedRenewal)->renewal_start;
}

public function getRenewalEndAttribute()
{
    return optional($this->latestApprovedRenewal)->renewal_end;
}

public function getBuriedAtAttribute()
{
    return optional($this->slot)->location_label;
}

}




