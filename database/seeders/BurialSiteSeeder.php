<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{BurialSite, Level, GraveCell, Slot};

class BurialSiteSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * ITO YUNG RULES
         * - rows: total number of rows in the level
         * - cols: default number of columns for each row
         * - slots: number of slots per cell
         * - cols_by_row (optional): [row_no => custom_cols]
         *     e.g. row 1 has 50 cols, row 2 has 49, others use `cols`
         */
        $sites = [
            'Left Side Restos' => [

                1 => [
                    'rows' => 8,
                    'cols' => 36,
                    'slots' => 3,
                    'cols_by_row' => [
                        1 => 35,
                        2 => 35,
                        3 => 35,
                        5 => 35,
                        6 => 34,
                        7 => 25,
                        8 => 2,
                    ],
                ],
                2 => [
                    'rows' => 7,
                    'cols' => 28,
                    'slots' => 3,
                    'cols_by_row' => [
                        6 => 27,
                        7 => 26

                    ],

                ],

                3 => [
                    'rows' => 7,
                     'cols' => 28,
                     'slots' => 3,
                     'cols_by_row' => [
                        6 => 24,
                        7 => 22,
                     ],

                    ],
                4 => [
                    'rows' => 6,
                     'cols' => 21,
                     'slots' => 3,
                     'cols_by_row' => [
                        6 => 18

                     ]

                    ],
                5 => [
                    'rows' => 7,
                    'cols' => 26,
                    'slots' => 3,
                    'cols_by_row' => [
                         5 => 25,
                         6 => 20,
                         7 => 6

                    ],

                ],
                6 => [
                    'rows' => 6,
                     'cols' => 19,
                     'slots' => 3,
                     'cols_by_row' => [
                          5 => 16,
                          6 => 16,
                     ],

                    ],
                // 7 => ['rows' => 7, 'cols' => 36, 'slots' => 3],
            ],

            'Left Side Entrance Apartment' => [
                1 => [
                    'rows' => 3,
                    'cols' => 50,
                    'slots' => 3,
                    'cols_by_row'=>[
                        2 => 49
                    ]
                ],

            ],

           'Left Side Apartment Veterans' => [
                1 => [
                    'rows' => 5,
                    'cols' => 5,
                    'slots' => 3,
                ],
            ],

            'Left Side Apartment IV' => [
                 1 => [
                    'rows' => 4,
                    'cols' => 86,
                    'slots' => 3,
                    'cols_by_row' =>[
                       2 => 83,
                       3 => 84,
                       4 => 31
                    ]
                ],
                  2 => [
                    'rows' => 3,
                    'cols' => 43,
                    'slots' => 3,
                    'cols_by_row' =>[
                       2 => 39,
                       3 => 24,

                    ]
                ],
            ],
               'Left Side Apartment III' => [
                1 => [
                    'rows' => 6,
                    'cols' => 76,
                    'slots' => 3,
                    'cols_by_row' => [
                       1 => 71,
                       2 => 69,
                       3 => 70,
                       4 => 73,
                       5 => 73,
                       6 => 76
                    ],
                ],
            ],

              'Left Side Apartment II' => [
                1 => [
                    'rows' => 6,
                    'cols' => 143,
                    'slots' => 3,
                    'cols_by_row' => [
                       1 => 138,
                       2 => 138,
                       3 => 138,
                       4 => 139,
                       6 => 1
                    ]
                ],
            ],

              'Right Side Apartment I-A' => [
                1 => [
                    'rows' => 4,
                    'cols' => 45,
                    'slots' => 3,
                    'cols_by_row' => [
                         2 => 44,
                         3 => 44
                    ]
                ],
            ],
             'Right Side Apartment I-B' => [
                1 => [
                    'rows' => 7,
                    'cols' => 168,
                    'slots' => 3,
                    'cols_by_row' => [
                         1 => 18,
                         2 => 49,
                         3 => 148,
                         4 => 151,
                         5 => 155,
                         7 => 166
                    ]
                ],
            ],

              'Right Side Apartment 5' => [
                1 => [
                    'rows' => 5,
                    'cols' => 90,
                    'slots' => 3,
                    'cols_by_row' => [
                         2 => 82,
                         3 => 83,
                         4 => 19,
                         5 => 11
                    ]
                ],
            ],

             'Right Side Entrance' => [
                1 => [
                    'rows' => 4,
                    'cols' => 46,
                    'slots' => 3,
                    'cols_by_row' => [
                         1 => 44,
                         2 => 44,
                         4 => 13,

                    ]
                ],
            ],





        ];

        DB::transaction(function () use ($sites) {
            foreach ($sites as $siteName => $levels) {

                $site = BurialSite::create(['name' => $siteName]);

                foreach ($levels as $levelNo => $cfg) {

                    $rows  = (int) ($cfg['rows']  ?? 0);
                    $cols  = (int) ($cfg['cols']  ?? 0);
                    $slots = (int) ($cfg['slots'] ?? 0);

                    if ($rows <= 0 || $cols <= 0 || $slots <= 0) {
                        throw new \InvalidArgumentException("Invalid config for {$siteName} level {$levelNo}");
                    }

                    /** @var \App\Models\Level $level */
                    $level = Level::create([
                        'burial_site_id' => $site->id,
                        'level_no'       => $levelNo,
                    ]);


                    $colsByRow = $cfg['cols_by_row'] ?? [];


                    $colsForRow = static function (int $rowNo) use ($cols, $colsByRow): int {
                        if (isset($colsByRow[$rowNo]) && (int)$colsByRow[$rowNo] > 0) {
                            return (int) $colsByRow[$rowNo];
                        }
                        return $cols;
                    };

                    foreach (range(1, $rows) as $row) {
                        $colCount = $colsForRow($row);

                        foreach (range(1, $colCount) as $col) {
                            $cell = GraveCell::create([
                                'level_id' => $level->id,
                                'row_no'   => $row,
                                'col_no'   => $col,
                            ]);

                            foreach (range(1, $slots) as $slotNo) {
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
