<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\{
    Reservation, Deceased, GraveDiggers, Verifier,
    BurialSite, Level, GraveCell, Slot, Family
};
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

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



    // public function store(Request $request)
    // {
    //     $v = $request->validate([

    //         'deceased_first_name'  => 'required|string|max:255',
    //         'deceased_middle_name' => 'nullable|string|max:255',
    //         'deceased_last_name'   => 'required|string|max:255',
    //         'deceased_suffix'      => 'nullable|string|max:50',
    //         'address_before_death' => 'required|string|max:255',
    //         'date_of_birth'        => 'required|date_format:Y-m-d',
    //         'date_of_death'        => 'required|date_format:Y-m-d|after_or_equal:date_of_birth',
    //         'sex'                  => 'required|in:MALE,FEMALE',


    //         'level_id'              => 'required|exists:levels,id',
    //         'slot_id'               => 'required|exists:slots,id',
    //         'grave_diggers_id'      => 'required|exists:grave_diggers,id',
    //         'verifiers_id'          => 'required|exists:verifiers,id',
    //         'burial_site_id'        => 'required|exists:burial_sites,id',


    //         'date_applied'          => 'required|date_format:Y-m-d',


    //         'internment_sched'      => [
    //             'required',
    //             'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/',
    //         ],

    //         'applicant_first_name'  => 'required|string|max:100',
    //         'applicant_middle_name' => 'nullable|string|max:100',
    //         'applicant_last_name'   => 'required|string|max:100',
    //         'applicant_suffix'      => 'nullable|string|max:20',

    //         'family_id'                 => 'nullable|exists:families,id',
    //         'applicant_address'         => 'nullable|string|max:255',
    //         'applicant_contact_no'      => 'nullable|string|max:50',
    //         'relationship_to_deceased'  => 'nullable|string|max:100',
    //         'amount_as_per_ord'         => 'nullable|string|max:50',
    //         'funeral_service'           => 'nullable|string|max:100',
    //         'other_info'                => 'nullable|string',
    //     ]);

    //     foreach (['deceased_first_name','deceased_middle_name','deceased_last_name','deceased_suffix',
    //               'applicant_first_name','applicant_middle_name','applicant_last_name','applicant_suffix'] as $k) {
    //         if (isset($v[$k])) $v[$k] = preg_replace('/\s+/', ' ', trim($v[$k]));
    //     }

    //     $v['date_of_birth']    = Carbon::parse($v['date_of_birth'])->format('Y-m-d');
    //     $v['date_of_death']    = Carbon::parse($v['date_of_death'])->format('Y-m-d');
    //     $v['date_applied']     = Carbon::parse($v['date_applied'])->format('Y-m-d');
    //     $v['internment_sched'] = Carbon::parse(str_replace('T',' ',$v['internment_sched']))->format('Y-m-d H:i:s');

    //     DB::transaction(function () use ($v) {

    //         $deceased = Deceased::create([
    //             'first_name'           => $v['deceased_first_name'],
    //             'middle_name'          => $v['deceased_middle_name'] ?? null,
    //             'last_name'            => $v['deceased_last_name'],
    //             'suffix'               => $v['deceased_suffix'] ?? null,
    //             'address_before_death' => $v['address_before_death'],
    //             'sex'                  => $v['sex'],
    //             'date_of_birth'        => $v['date_of_birth'],
    //             'date_of_death'        => $v['date_of_death'],
    //         ]);


    //         $familyId = $v['family_id'] ?? null;
    //         if (!$familyId) {
    //             $last = strtoupper(trim($v['deceased_last_name'] ?? ''));
    //             if ($last === '') {
    //                 $last = strtoupper(trim($v['applicant_last_name'] ?? 'UNSPECIFIED'));
    //             }
    //             $family = Family::firstOrCreate(['name' => $last . ' FAMILY']);
    //             $familyId = $family->id;
    //         }



    //         $slot = Slot::lockForUpdate()->findOrFail($v['slot_id']);
    //         if ($slot->status !== 'available') {
    //             throw \Illuminate\Validation\ValidationException::withMessages([
    //                 'slot_id' => ['Selected slot is no longer available.'],
    //             ]);
    //         }


    //         $cell = GraveCell::lockForUpdate()->find($slot->grave_cell_id);

    //         $activeRes = Reservation::active()
    //             ->whereHas('slot', fn($q) => $q->where('grave_cell_id', $cell->id))
    //             ->whereNotNull('family_id')
    //             ->first();

    //         $cellOwnerId = $cell->family_id ?: optional($activeRes)->family_id;

    //         if ($cellOwnerId && (int)$cellOwnerId !== (int)$familyId) {
    //             throw \Illuminate\Validation\ValidationException::withMessages([
    //                 'slot_id' => ['This grave cell is reserved for another family. Please choose a different cell.'],
    //             ]);
    //         }

    //         if (!$cellOwnerId) {
    //             $cell->update(['family_id' => $familyId]);
    //         }

    //         $slot->update(['status' => 'occupied']);

    //         Reservation::create([
    //             'level_id'                 => $v['level_id'],
    //             'burial_site_id'           => $v['burial_site_id'],
    //             'deceased_id'              => $deceased->id,
    //             'grave_diggers_id'         => $v['grave_diggers_id'],
    //             'verifiers_id'             => $v['verifiers_id'],
    //             'slot_id'                  => $slot->id,
    //             'family_id'                => $familyId,

    //             'date_applied'             => $v['date_applied'],

    //             'applicant_first_name'     => $v['applicant_first_name'],
    //             'applicant_middle_name'    => $v['applicant_middle_name'] ?? null,
    //             'applicant_last_name'      => $v['applicant_last_name'],
    //             'applicant_suffix'         => $v['applicant_suffix'] ?? null,

    //             'applicant_address'        => $v['applicant_address'] ?? null,
    //             'applicant_contact_no'     => $v['applicant_contact_no'] ?? null,
    //             'relationship_to_deceased' => $v['relationship_to_deceased'] ?? null,
    //             'amount_as_per_ord'        => $v['amount_as_per_ord'] ?? null,
    //             'funeral_service'          => $v['funeral_service'] ?? null,
    //             'other_info'               => $v['other_info'] ?? null,
    //             'internment_sched'         => $v['internment_sched'],
    //         ]);
    //     });

    //     return redirect()->route('burial_application_form')->with('success','Reservation saved successfully!');
    // }

     public function store(Request $request)
    {
        $v = $request->validate([
            'no_lapida'             => 'nullable|in:0,1',

            'deceased_first_name'   => 'required_without:no_lapida|string|max:255|nullable',
            'deceased_middle_name'  => 'nullable|string|max:255',
            'deceased_last_name'    => 'required_without:no_lapida|string|max:255|nullable',
            'deceased_suffix'       => 'nullable|string|max:50',
            'address_before_death'  => 'required_without:no_lapida|string|max:255|nullable',
            'date_of_birth'         => 'required_without:no_lapida|date_format:Y-m-d|nullable',
            'date_of_death'         => 'required_without:no_lapida|date_format:Y-m-d|after_or_equal:date_of_birth|nullable',
            'sex'                   => 'required_without:no_lapida|in:MALE,FEMALE|nullable',

            'level_id'              => 'required|exists:levels,id',
            'slot_id'               => 'required|exists:slots,id',
            'grave_diggers_id'      => 'required|exists:grave_diggers,id',
            'verifiers_id'          => 'required|exists:verifiers,id',
            'burial_site_id'        => 'required|exists:burial_sites,id',

            'date_applied'          => 'required|date_format:Y-m-d',
            'internment_sched'      => ['required','regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(:\d{2})?$/'],

            'applicant_first_name'  => 'required|string|max:100',
            'applicant_middle_name' => 'nullable|string|max:100',
            'applicant_last_name'   => 'required|string|max:100',
            'applicant_suffix'      => 'nullable|string|max:20',

            'family_id'                 => 'nullable|exists:families,id',
            'applicant_address'         => 'nullable|string|max:255',
            'applicant_contact_no'      => 'nullable|string|max:50',
            'relationship_to_deceased'  => 'required|string|max:100',
            'amount_as_per_ord'         => 'nullable|string|max:50',
            'funeral_service'           => 'nullable|string|max:100',
            'other_info'                => 'nullable|string',
        ]);

        foreach ([
            'deceased_first_name','deceased_middle_name','deceased_last_name','deceased_suffix',
            'applicant_first_name','applicant_middle_name','applicant_last_name','applicant_suffix'
        ] as $k) {
            if (isset($v[$k])) $v[$k] = preg_replace('/\s+/', ' ', trim($v[$k]));
        }

        $noLapida = (string)($v['no_lapida'] ?? '0') === '1';

        $v['date_applied'] = Carbon::parse($v['date_applied'])->format('Y-m-d');
        $v['internment_sched'] = Carbon::parse(str_replace('T',' ',$v['internment_sched']))->format('Y-m-d H:i:s');

        if (!$noLapida) {
            $v['date_of_birth'] = Carbon::parse($v['date_of_birth'])->format('Y-m-d');
            $v['date_of_death'] = Carbon::parse($v['date_of_death'])->format('Y-m-d');
        } else {

            $v['deceased_first_name'] = 'NO LAPIDA';
            $v['deceased_middle_name'] = null;
            $v['deceased_last_name'] = null;
            $v['deceased_suffix'] = null;
            $v['sex'] = null;
            $v['date_of_birth'] = null;
            $v['date_of_death'] = null;
            $v['address_before_death'] = $v['address_before_death'] ?? null;
        }

        DB::transaction(function () use ($v) {
            $deceased = Deceased::create([
                'first_name'           => $v['deceased_first_name'],
                'middle_name'          => $v['deceased_middle_name'] ?? null,
                'last_name'            => $v['deceased_last_name'],
                'suffix'               => $v['deceased_suffix'] ?? null,
                'address_before_death' => $v['address_before_death'] ?? null,
                'sex'                  => $v['sex'] ?? null,
                'date_of_birth'        => $v['date_of_birth'] ?? null,
                'date_of_death'        => $v['date_of_death'] ?? null,
            ]);

            // Family logic
            $familyId = $v['family_id'] ?? null;
            if (!$familyId) {
                $last = strtoupper(trim($v['deceased_last_name'] ?? ''));
                if ($last === '') {
                    $last = strtoupper(trim($v['applicant_last_name'] ?? 'UNSPECIFIED'));
                }
                $family = Family::firstOrCreate(['name' => $last . ' FAMILY']);
                $familyId = $family->id;
            }

            // Lock and check Slot
            $slot = Slot::lockForUpdate()->findOrFail($v['slot_id']);
            if ($slot->status !== 'available') {
                throw ValidationException::withMessages([
                    'slot_id' => ['Selected slot is no longer available.'],
                ]);
            }

            $cell = GraveCell::lockForUpdate()->find($slot->grave_cell_id);

            $activeRes = Reservation::active()
                ->whereHas('slot', fn($q) => $q->where('grave_cell_id', $cell->id))
                ->whereNotNull('family_id')
                ->first();

            $cellOwnerId = $cell->family_id ?: optional($activeRes)->family_id;

            if ($cellOwnerId && (int)$cellOwnerId !== (int)$familyId) {
                throw ValidationException::withMessages([
                    'slot_id' => ['This grave cell is reserved for another family. Please choose a different cell.'],
                ]);
            }

            if (!$cellOwnerId) {
                $cell->update(['family_id' => $familyId]);
            }

            $slot->update(['status' => 'occupied']);

            // Create Reservation
            Reservation::create([
                'level_id'                 => $v['level_id'],
                'burial_site_id'           => $v['burial_site_id'],
                'deceased_id'              => $deceased->id,
                'grave_diggers_id'         => $v['grave_diggers_id'],
                'verifiers_id'             => $v['verifiers_id'],
                'slot_id'                  => $slot->id,
                'family_id'                => $familyId,

                'date_applied'             => $v['date_applied'],

                'applicant_first_name'     => $v['applicant_first_name'],
                'applicant_middle_name'    => $v['applicant_middle_name'] ?? null,
                'applicant_last_name'      => $v['applicant_last_name'],
                'applicant_suffix'         => $v['applicant_suffix'] ?? null,

                'applicant_address'        => $v['applicant_address'] ?? null,
                'applicant_contact_no'     => $v['applicant_contact_no'] ?? null,
                'relationship_to_deceased' => $v['relationship_to_deceased'],
                'amount_as_per_ord'        => $v['amount_as_per_ord'] ?? null,
                'funeral_service'          => $v['funeral_service'] ?? null,
                'other_info'               => $v['other_info'] ?? null,
                'internment_sched'         => $v['internment_sched'],
            ]);
        });

        return redirect()->route('burial_application_form')->with('success','Reservation saved successfully!');
    }



    public function levels(BurialSite $site)  { return $site->levels()->select('id','level_no')->get(); }
    public function cells(Level $level)       { return $level->cells()->select('id','row_no','col_no')->get(); }
    public function slots(GraveCell $cell)    { return $cell->slots()->where('status','available')->select('id','slot_no')->get(); }
}
