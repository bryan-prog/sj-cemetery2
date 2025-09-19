<?php

namespace App\Support;

use App\Models\{Slot, Renewal, Reservation};
use Carbon\Carbon;

class CellCoverage
{
    protected static array $cache = [];

    public function coverageEndForCell(int $cellId, ?Carbon $asOf = null): ?Carbon
    {
        $asOf = ($asOf ?: now())->startOfDay();
        $key  = "{$cellId}@{$asOf->toDateString()}";

        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');
        if ($slotIds->isEmpty()) {
            return self::$cache[$key] = null;
        }


        $latestApprovedEnd = Renewal::whereIn('slot_id', $slotIds)
            ->whereRaw('LOWER(status) = ?', ['approved'])
            ->max('renewal_end');

        if ($latestApprovedEnd) {
            return self::$cache[$key] = Carbon::parse($latestApprovedEnd)->startOfDay();
        }


        $earliestOcc = Slot::whereIn('id', $slotIds)
            ->whereNotNull('occupancy_start')
            ->min('occupancy_start');

        $earliestInt = Reservation::whereIn('slot_id', $slotIds)
            ->whereNotNull('internment_sched')
            ->min('internment_sched');

        $earliestRen = Renewal::whereIn('slot_id', $slotIds)
            ->whereNotNull('renewal_start')
            ->min('renewal_start');

        $candidates = array_filter([$earliestOcc, $earliestInt, $earliestRen]);
        if (!$candidates) {
            return self::$cache[$key] = null;
        }

        $anchor = Carbon::parse(min($candidates))->startOfDay();
        return self::$cache[$key] = $anchor->copy()->addYears(5);
    }


    public function cellWindow(int $cellId, ?Carbon $asOf = null): array
    {
        $asOf = ($asOf ?: now())->startOfDay();


        $slotIds = Slot::where('grave_cell_id', $cellId)->pluck('id');
        if ($slotIds->isNotEmpty()) {
            $hasPending = Renewal::whereIn('slot_id', $slotIds)
                ->whereRaw('LOWER(status) = ?', ['pending'])
                ->exists();
            if ($hasPending) {
                return ['state' => 'covered', 'coverageEnd' => $this->coverageEndForCell($cellId, $asOf)];
            }
        }

        $coverageEnd = $this->coverageEndForCell($cellId, $asOf);


        if (!$coverageEnd) {
            return ['state' => 'covered', 'coverageEnd' => null];
        }

        if ($asOf->lte($coverageEnd)) {
            return ['state' => 'covered', 'coverageEnd' => $coverageEnd];
        }

        $graceEnd = $coverageEnd->copy()->addYear();
        if ($asOf->lte($graceEnd)) {
            return ['state' => 'grace', 'coverageEnd' => $coverageEnd];
        }

        return ['state' => 'penalty', 'coverageEnd' => $coverageEnd];
    }


    public function cellIsCovered(int $cellId, ?Carbon $asOf = null): bool
    {
        return $this->cellWindow($cellId, $asOf)['state'] === 'covered';
    }


    public function cellInGrace(int $cellId, ?Carbon $asOf = null): bool
    {
        return $this->cellWindow($cellId, $asOf)['state'] === 'grace';
    }
}
