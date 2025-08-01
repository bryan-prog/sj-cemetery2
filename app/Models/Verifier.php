<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Verifier extends Model
{
    use HasFactory;

    protected $table = 'verifiers';


    protected $fillable = [
        'name_of_verifier',
        'position'
    ];


      public function reservation()
    {
        return $this->hasMany(Reservation::class, 'verifiers_id');
    }





}
