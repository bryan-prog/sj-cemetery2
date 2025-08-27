<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Level, BurialSite, Verifier};

class LevelController extends Controller
{

    public function grid(Level $level)
    {

        $level->load([
            'apartment',
            'cells.family',
            'cells.slots.renewals',
            'cells.slots.reservation.deceased',
            'cells.slots.reservation.exhumations',
        ]);

        $burial_sites = BurialSite::orderBy('name')->get();
        $verifiers    = Verifier::orderBy('name_of_verifier')->get();

        return view('Level.grid', compact('level', 'burial_sites', 'verifiers'));
    }


    public function reserve(Request $request, Level $level)
    {
        $level->load([
            'apartment',
            'cells.family',
            'cells.slots.renewals',
            'cells.slots.reservation.deceased',
             'cells.slots.reservation.exhumations',
        ]);

        $burial_sites = BurialSite::orderBy('name')->get();
        $verifiers    = Verifier::orderBy('name_of_verifier')->get();

        return view('Level.grid', compact('level', 'burial_sites', 'verifiers'));
    }


    public function show(int $levelNo)
    {
        $level = Level::with([
                    'apartment',
                    'cells.family',
                    'cells.slots.renewals',
                    'cells.slots.reservation.deceased',
                     'cells.slots.reservation.exhumations',
                 ])
                 ->where('level_no', $levelNo)
                 ->firstOrFail();

        $burial_sites = BurialSite::orderBy('name')->get();
        $verifiers    = Verifier::orderBy('name_of_verifier')->get();

        return view('Level.grid', compact('level', 'burial_sites', 'verifiers'));
    }
}
