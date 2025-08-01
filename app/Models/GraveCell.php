<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraveCell extends Model
{
    protected $fillable = ['level_id','row_no','col_no','has_three_slots'];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
    // public function slotsRemaining(): int
    // {
    //     return $this->slot_capacity - $this->slots()->count();
    // }
}
