<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{

    protected $fillable = ['burial_site_id', 'level_no'];


    public function apartment()
    {

        return $this->belongsTo(BurialSite::class, 'burial_site_id', 'id');
    }


    public function burialSite()
    {
        return $this->apartment();
    }

    public function cells()
    {
        return $this->hasMany(GraveCell::class, 'level_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'level_id');
    }
}
