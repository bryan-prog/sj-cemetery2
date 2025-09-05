<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservations';

    protected $fillable = [
        'level_id','deceased_id','grave_diggers_id','burial_site_id','verifiers_id',
        'slot_id','date_applied',
        'applicant_first_name','applicant_middle_name','applicant_last_name','applicant_suffix',
        'applicant_address','applicant_contact_no',
        'relationship_to_deceased','amount_as_per_ord','funeral_service','renewal_date',
        'other_info','internment_sched', 'family_id'
    ];

    protected $appends = [
        'renewal_start','renewal_end','buried_at','applicant_name',
        'apt_level_label','location_or_apt_level',
    ];

    protected $casts = [
        'date_applied'     => 'date',
        'internment_sched' => 'datetime',
        'renewal_date'     => 'date',
    ];

    public function level()         { return $this->belongsTo(Level::class, 'level_id'); }
    public function burial_sites()  { return $this->belongsTo(BurialSite::class, 'burial_site_id'); }
    public function burialSite()    { return $this->belongsTo(BurialSite::class, 'burial_site_id'); }
    public function deceased()      { return $this->belongsTo(Deceased::class, 'deceased_id'); }
    public function grave_diggers() { return $this->belongsTo(GraveDiggers::class, 'grave_diggers_id'); }
    public function verifiers()     { return $this->belongsTo(Verifier::class, 'verifiers_id'); }
    public function slot()          { return $this->belongsTo(Slot::class, 'slot_id'); }
    public function family()        { return $this->belongsTo(Family::class); }

    public function renewals()
    {
        return $this->hasMany(Renewal::class)->oldest('renewal_start');
    }

    public function latestApprovedRenewal()
    {
        return $this->hasOne(Renewal::class)
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->latestOfMany();
    }

    public function getRenewalStartAttribute() { return optional($this->latestApprovedRenewal)->renewal_start; }
    public function getRenewalEndAttribute()   { return optional($this->latestApprovedRenewal)->renewal_end; }
    public function getBuriedAtAttribute()     { return optional($this->slot)->location_label; }

    public function getApplicantNameAttribute(): string
    {
        $parts = array_filter([
            $this->applicant_first_name,
            $this->applicant_middle_name,
            $this->applicant_last_name,
            $this->applicant_suffix,
        ], fn($v) => (string)$v !== '');
        return trim(implode(' ', $parts));
    }

    public function exhumations() { return $this->hasMany(Exhumation::class); }

    public function scopeActive($query)
    {
        return $query->whereDoesntHave('exhumations', function ($q) {
            $q->whereRaw('LOWER(status) = ?', ['approved'])
              ->whereNull('to_slot_id');
        });
    }

    public function scopeForSiteLevelByName(Builder $query, string $siteName, int $levelNo): Builder
    {
        return $query
            ->whereHas('burialSite', fn ($q) => $q->where('name', $siteName))
            ->whereHas('level', fn ($q) => $q->where('level_no', $levelNo));


    }

    public function scopeForSiteLevel(Builder $query, int $burialSiteId, int $levelNo): Builder
    {
        return $query
            ->where('burial_site_id', $burialSiteId)
            ->whereHas('level', fn ($q) => $q->where('level_no', $levelNo));
    }

    // --------- NEW computed labels ---------

    public function getAptLevelLabelAttribute(): ?string
    {
        $level = $this->slot?->cell?->level ?: $this->level;
        if (!$level) return $this->burialSite?->name;

        $apt = $level->apartment?->name ?? $this->burialSite?->name;
        if (!$apt) return null;

        $n = (int) $level->level_no;
        $v = $n % 100;
        $ord = ($v>=11 && $v<=13) ? "{$n}th" : $n . (['th','st','nd','rd','th','th','th','th','th','th'][$n%10]);

        return $apt . ', ' . $ord . ' Level';
    }

    public function getLocationOrAptLevelAttribute(): ?string
    {
        // Prefer precise slot label if your Slot model exposes 'location_label'
        return $this->slot?->location_label
            ?: $this->apt_level_label
            ?: ($this->burialSite?->name);
    }
}
