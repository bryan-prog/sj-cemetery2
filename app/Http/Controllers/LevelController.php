<?php

namespace App\Http\Controllers;

use App\Models\{Level, BurialSite, Verifier};
use Illuminate\Database\Eloquent\Builder;

class LevelController extends Controller
{

    public function grid(Level $level)
    {

        $level->load([
            'apartment',
            'cells.slots.renewals',
            'cells.slots.reservation.deceased',
        ]);

        $burial_sites = BurialSite::orderBy('name')->get();
        $verifiers    = Verifier::orderBy('name_of_verifier')->get();

        return view('Level.grid', compact('level', 'burial_sites', 'verifiers'));
    }


    public function show(int $levelNo)
    {
        $level = Level::with([
                    'apartment',
                    'cells.slots.renewals',
                    'cells.slots.reservation.deceased',
                 ])
                 ->where('level_no', $levelNo)
                 ->firstOrFail();

        $burial_sites = BurialSite::orderBy('name')->get();
        $verifiers    = Verifier::orderBy('name_of_verifier')->get();

        return view('Level.grid', compact('level', 'burial_sites', 'verifiers'));
    }
}
