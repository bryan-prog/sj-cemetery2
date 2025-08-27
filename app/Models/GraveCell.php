<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GraveCell extends Model
{
    protected $fillable = ['level_id','row_no','col_no','has_three_slots', 'family_id', 'max_slots'];

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function family()
    {
        return $this->belongsTo(\App\Models\Family::class);
    }

    public function nextSlotNo(): int
    {
        return (int) ($this->slots()->max('slot_no') ?? 0) + 1;
    }
}
