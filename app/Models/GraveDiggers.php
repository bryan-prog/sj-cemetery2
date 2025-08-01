<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraveDiggers extends Model
{
    protected $table      = 'grave_diggers';

    protected $fillable   = ['name'];

     public function reservation()
    {
        return $this->hasMany(Reservation::class, 'grave_diggers_id');
    }

}
