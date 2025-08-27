<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deceased extends Model
{
    use HasFactory;

    protected $table = 'deceased';

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'address_before_death',
        'sex',
        'date_of_birth',
        'date_of_death',
    ];

    protected $appends = ['full_name', 'name_of_deceased'];

    public function getFullNameAttribute(): string
    {
        return trim(collect([
            $this->first_name,
            $this->middle_name,
            $this->last_name,
            $this->suffix,
        ])->filter()->implode(' '));
    }


    public function getNameOfDeceasedAttribute(): string
    {
        return $this->full_name;
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class, 'deceased_id');
    }
}
