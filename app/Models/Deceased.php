<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deceased extends Model
{
    use HasFactory;


    protected $table = 'deceased';

    protected $fillable = [
        'name_of_deceased',
        'address_before_death',
        'sex',
        'date_of_birth',
        'date_of_death',
    ];

    public function reservation()
    {
        return $this->hasMany(Reservation::class, 'deceased_id');
    }
}
