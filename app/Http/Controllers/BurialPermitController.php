<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Reservation, Deceased, GraveDiggers, Verifier,
    BurialSite, Level, GraveCell, Slot
};

use Carbon\Carbon;

class BurialPermitController extends Controller
{

 public function burial_application_form()
    {
        return view('burial_application_form', [
            'grave_diggers' => GraveDiggers::orderBy('name')->get(),
            'verifiers'     => Verifier::orderBy('name_of_verifier')->get(),
            'burial_sites'  => BurialSite::orderBy('name')->get(),
        ]);
    }


    public function createGrid(Request $request, Level $level)
    {
        $level->load('apartment', 'cells.slots.reservation.deceased');


        $burial_sites = BurialSite::orderBy('name')->get();
        $verifiers    = Verifier::orderBy('name_of_verifier')->get();

        return view('Level.grid', compact('level', 'burial_sites', 'verifiers'));
    }

    public function store(Request $request)
    {
        $v = $request->validate([
            'name_of_deceased'      => 'required|string|max:255',
            'address_before_death'  => 'required|string|max:255',
            'date_of_birth'         => 'required|date_format:Y-m-d',
            'date_of_death'         => 'required|date_format:Y-m-d|after_or_equal:date_of_birth',
            'sex'                   => 'required|in:MALE,FEMALE',

            'level_id'              => 'required|exists:levels,id',
            'slot_id'               => 'required|exists:slots,id',
            'grave_diggers_id'      => 'required|exists:grave_diggers,id',
            'verifiers_id'          => 'required|exists:verifiers,id',
            'burial_site_id'        => 'required|exists:burial_sites,id',
            'date_applied'          => 'required|date_format:Y-m-d',
            'applicant_name'        => 'required|string|max:255',

            'internment_sched' => [
                'required',
                'after_or_equal:date_applied',
                'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/',
            ],

            'applicant_address'         => 'nullable|string|max:255',
            'applicant_contact_no'      => 'nullable|string|max:50',
            'relationship_to_deceased'  => 'nullable|string|max:100',
            'amount_as_per_ord'         => 'nullable|string|max:50',
            'funeral_service'           => 'nullable|string|max:100',
            'other_info'                => 'nullable|string',
        ]);


        $v['date_of_birth']    = Carbon::parse($v['date_of_birth'])->format('Y-m-d');
        $v['date_of_death']    = Carbon::parse($v['date_of_death'])->format('Y-m-d');
        $v['date_applied']     = Carbon::parse($v['date_applied'])->format('Y-m-d');
        $v['internment_sched'] = Carbon::parse(str_replace('T',' ',$v['internment_sched']))
                                      ->format('Y-m-d H:i:s');

        DB::transaction(function () use ($v) {

            $deceased = Deceased::create([
                'name_of_deceased'     => $v['name_of_deceased'],
                'address_before_death' => $v['address_before_death'],
                'sex'                  => $v['sex'],
                'date_of_birth'        => $v['date_of_birth'],
                'date_of_death'        => $v['date_of_death'],
            ]);


            $slot = Slot::lockForUpdate()->findOrFail($v['slot_id']);
            if ($slot->status !== 'available') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'slot_id' => ['Selected slot is no longer available.'],
                ]);
            }
            $slot->update(['status' => 'occupied']);


            Reservation::create([
                'level_id'                 => $v['level_id'],
                'burial_site_id'           => $v['burial_site_id'],
                'deceased_id'              => $deceased->id,
                'grave_diggers_id'         => $v['grave_diggers_id'],
                'verifiers_id'             => $v['verifiers_id'],
                'slot_id'                  => $slot->id,
                'date_applied'             => $v['date_applied'],
                'applicant_name'           => $v['applicant_name'],
                'applicant_address'        => $v['applicant_address'],
                'applicant_contact_no'     => $v['applicant_contact_no'],
                'relationship_to_deceased' => $v['relationship_to_deceased'],
                'amount_as_per_ord'        => $v['amount_as_per_ord'],
                'funeral_service'          => $v['funeral_service'],
                'other_info'               => $v['other_info'],
                'internment_sched'         => $v['internment_sched'],
            ]);
        });

        return redirect()
               ->route('burial_application_form')
               ->with('success','Reservation saved successfully!');
    }
    public function levels(BurialSite $site)
    {
        return $site->levels()->select('id','level_no')->get();
    }


    public function cells(Level $level)
    {
        return $level->cells()->select('id','row_no','col_no')->get();
    }

    public function slots(GraveCell $cell)
    {
        return $cell->slots()
                    ->where('status','available')
                    ->select('id','slot_no')
                    ->get();
    }

}
