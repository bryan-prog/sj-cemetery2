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
    // Respect transient states
    if (in_array($this->status, ['renewal_pending','exhumation_pending'], true)) {
        return $this->status;
    }

    // Available-like stay as-is
    if ($this->status === 'available' || $this->status === 'exhumed') {
        return $this->status;
    }

    // Compute window for occupied slots
    if ($this->status === 'occupied') {
        $cellId = (int) $this->grave_cell_id;

        /** @var \App\Support\CellCoverage $cov */
        $cov = app(\App\Support\CellCoverage::class);
        $window = $cov->cellWindow($cellId, \Carbon\Carbon::now());

        switch ($window['state']) {
            case 'covered':
                return 'occupied';
            case 'grace':
                // Past 5 years by ≥1 day but within 1 year → show teal
                return 'for_renewal';
            case 'penalty':
            default:
                return 'for_penalty';
        }
    }

    // Fallback
    return $this->status;
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
