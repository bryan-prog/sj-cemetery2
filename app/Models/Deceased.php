<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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



    protected $appends = ['full_name', 'name_of_deceased', 'dob_ymd', 'dod_ymd'];

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


    private function normalizeDateOnly($value): ?string
    {
        if ($value === null) return null;
        $s = trim((string)$value);


        if ($s === '' || preg_match('/^0{4}-0{2}-0{2}/', $s)) return null;

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {

            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $s, $m)) return $m[0];
            return null;
        }
    }


    public function getDobYmdAttribute(): ?string
    {
        return $this->normalizeDateOnly($this->getAttribute('date_of_birth'));
    }

    public function getDodYmdAttribute(): ?string
    {
        return $this->normalizeDateOnly($this->getAttribute('date_of_death'));
    }

    public function reservation()
    {
        return $this->hasMany(Reservation::class, 'deceased_id');
    }
}
