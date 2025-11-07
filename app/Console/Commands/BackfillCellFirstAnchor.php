<?php
namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\{GraveCell, Slot, Reservation};
use Carbon\Carbon;

class BackfillCellFirstAnchor extends Command
{
    protected $signature = 'cells:backfill-first-anchor';
    protected $description = 'Set first_anchor_at = earliest internment/occupancy per cell';

    public function handle(): int
    {
        GraveCell::with([])->chunkById(500, function ($cells) {
            foreach ($cells as $cell) {
                if ($cell->first_anchor_at) continue;

                $slotIds = Slot::where('grave_cell_id', $cell->id)->pluck('id');

                $earliestOcc = Slot::whereIn('id', $slotIds)
                    ->whereNotNull('occupancy_start')->min('occupancy_start');

                $earliestInt = Reservation::whereIn('slot_id', $slotIds)
                    ->whereNotNull('internment_sched')->min('internment_sched');

                $anchorRaw = collect([$earliestOcc, $earliestInt])
                    ->filter()->map(fn($d)=>Carbon::parse($d)->startOfDay())->min();

                if ($anchorRaw) {
                    $cell->first_anchor_at = $anchorRaw;
                    $cell->save();
                    $this->line("Cell {$cell->id} -> {$cell->first_anchor_at->toDateString()}");
                }
            }
        });

        $this->info('Done.');
        return self::SUCCESS;
    }
}
