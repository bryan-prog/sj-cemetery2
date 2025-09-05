<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name', 'suffix',
        'contact_person', 'contact_no', 'address',
    ];


    protected $appends = ['name'];


    public function getNameAttribute(): string
    {
        $last = strtoupper(trim((string)$this->last_name));
        return $last !== '' ? ($last . ' FAMILY') : 'FAMILY';
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function graveCells()
    {
        return $this->hasMany(\App\Models\GraveCell::class);
    }
}
