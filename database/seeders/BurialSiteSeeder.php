<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{BurialSite, Level, GraveCell, Slot};

class BurialSiteSeeder extends Seeder
{
    public function run(): void
    {

        $sites = [
            'Restos' => [

                1 => ['rows' => 8, 'cols' => 36, 'slots' => 3],
                2 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
                3 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
                4 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
                5 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
                6 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
                7 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
            ],

            'Apartment IV' => [
                1 => ['rows' => 10, 'cols' => 20, 'slots' => 2],
                2 => ['rows' => 10, 'cols' => 20, 'slots' => 2],
            ],


        ];

        DB::transaction(function () use ($sites) {

            foreach ($sites as $siteName => $levels) {

                $site = BurialSite::create(['name' => $siteName]);

                foreach ($levels as $levelNo => $cfg) {

                    $level = Level::create([
                        'burial_site_id' => $site->id,
                        'level_no'       => $levelNo,
                    ]);

                    foreach (range(1, $cfg['rows']) as $row) {
                        foreach (range(1, $cfg['cols']) as $col) {

                            $cell = GraveCell::create([
                                'level_id' => $level->id,
                                'row_no'   => $row,
                                'col_no'   => $col,
                            ]);

                            foreach (range(1, $cfg['slots']) as $slotNo) {
                                Slot::create([
                                    'grave_cell_id' => $cell->id,
                                    'slot_no'       => $slotNo,
                                ]);
                            }
                        }
                    }
                }
            }
        });
    }
}
