<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Slot extends Model
{
    protected $fillable = [
        'grave_cell_id','slot_no','status',
        'occupancy_start','occupancy_end',
    ];


    protected $appends = ['display_status'];


    public function cell()        { return $this->belongsTo(GraveCell::class, 'grave_cell_id'); }
    public function reservation() { return $this->hasOne(Reservation::class,'slot_id'); }
    public function exhumation()  { return $this->hasOne(Exhumation::class,'from_slot_id')->latest(); }

public function renewals()
{

    return $this->hasMany(Renewal::class)->latest('id');
}

public function getDisplayStatusAttribute(): string
{
    if (in_array($this->status, ['renewal_pending','exhumation_pending'])) {
        return $this->status;
    }

    $now = Carbon::now()->startOfDay();

    $internment = optional($this->reservation)->internment_sched
                 ? Carbon::parse($this->reservation->internment_sched)
                 : null;


    $latestRenewal = $this->renewals()
                          ->whereRaw('LOWER(status) = ?', ['approved'])
                          ->first();

    $renewalEnd   = $latestRenewal?->renewal_end;

    $coverageEnd  = $renewalEnd
                  ?? $internment?->copy()->addYears(5);

    return ($coverageEnd && $coverageEnd->lt($now))
           ? 'for_penalty'
           : $this->status;
}


public function getLocationLabelAttribute(): ?string
{

    $this->loadMissing('cell.level.apartment');

    $cell  = $this->cell;
    $level = $cell?->level;
    $site  = $level?->apartment;

    if (!$cell || !$level) {
        return null;
    }

    return sprintf(
        '%s • L%s R%s C%s S%s',
        $site?->name ?? '—',
        $level->level_no,
        $cell->row_no,
        $cell->col_no,
        $this->slot_no
    );
}



}
