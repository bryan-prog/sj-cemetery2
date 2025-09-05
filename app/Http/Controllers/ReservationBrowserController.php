<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\BurialSite;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservationExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationBrowserController extends Controller
{
    public function index()
    {
        $burialSites = BurialSite::orderBy('name')->get(['id','name']);
        return view('reservations.index', compact('burialSites'));
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['deceased', 'burialSite:id,name', 'level', 'slot.cell.level.apartment']);

        $level    = $reservation->slot?->cell?->level ?: $reservation->level;
        $aptName  = $level?->apartment?->name ?? $reservation->burialSite?->name;
        $levelNo  = $level?->level_no;
        $location = $reservation->location_or_apt_level ?? ($aptName ? "Apartment: {$aptName} Level: {$levelNo}" : '—');

        $decName = trim(implode(' ', array_filter([
            $reservation->deceased?->first_name,
            $reservation->deceased?->middle_name,
            $reservation->deceased?->last_name,
            $reservation->deceased?->suffix,
        ])));

        return response()->json([
            'id'                    => $reservation->id,
            'date_applied'          => optional($reservation->date_applied)->format('Y-m-d'),

            'internment_sched'      => optional($reservation->internment_sched)->format('Y-m-d H:i:s'),
            'applicant_name'        => $reservation->applicant_name,

            'applicant_first_name'  => $reservation->applicant_first_name,
            'applicant_middle_name' => $reservation->applicant_middle_name,
            'applicant_last_name'   => $reservation->applicant_last_name,
            'applicant_suffix'      => $reservation->applicant_suffix,
            'applicant_address'     => $reservation->applicant_address,
            'applicant_contact_no'  => $reservation->applicant_contact_no,
            'relationship_to_deceased' => $reservation->relationship_to_deceased,

            'deceased_name'         => $decName,
            'deceased_first_name'   => $reservation->deceased?->first_name,
            'deceased_middle_name'  => $reservation->deceased?->middle_name,
            'deceased_last_name'    => $reservation->deceased?->last_name,
            'deceased_suffix'       => $reservation->deceased?->suffix,
            'date_of_birth'         => $reservation->deceased?->dob_ymd,
            'date_of_death'         => $reservation->deceased?->dod_ymd,

            'burial_site'           => $aptName,
            'level_no'              => $levelNo,
            'location'              => $location,

            'funeral_service'       => $reservation->funeral_service,
            'amount_as_per_ord'     => $reservation->amount_as_per_ord,
            'other_info'            => $reservation->other_info,
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->load(['deceased']);


        $v = $request->validate([
            'date_applied'             => ['required','date_format:Y-m-d'],


            'internment_sched'         => ['required'],

            'applicant_first_name'     => ['nullable','string','max:100'],
            'applicant_middle_name'    => ['nullable','string','max:100'],
            'applicant_last_name'      => ['nullable','string','max:100'],
            'applicant_suffix'         => ['nullable','string','max:20'],
            'applicant_address'        => ['nullable','string','max:255'],
            'applicant_contact_no'     => ['nullable','string','max:50'],
            'relationship_to_deceased' => ['nullable','string','max:100'],

            'deceased_first_name'      => ['nullable','string','max:255'],
            'deceased_middle_name'     => ['nullable','string','max:255'],
            'deceased_last_name'       => ['nullable','string','max:255'],
            'deceased_suffix'          => ['nullable','string','max:50'],
            'date_of_birth'            => ['required','date_format:Y-m-d'],
            'date_of_death'            => ['required','date_format:Y-m-d','after_or_equal:date_of_birth'],

            'amount_as_per_ord'        => ['nullable','string','max:50'],
            'funeral_service'          => ['nullable','string','max:100'],
            'other_info'               => ['nullable','string'],
        ]);

        foreach ([
            'applicant_first_name','applicant_middle_name','applicant_last_name','applicant_suffix',
            'deceased_first_name','deceased_middle_name','deceased_last_name','deceased_suffix'
        ] as $k) {
            if (isset($v[$k])) {
                $v[$k] = preg_replace('/\s+/', ' ', trim($v[$k]));
            }
        }


        $raw = $v['internment_sched'];
        $internment = null;
        foreach (['Y-m-d\TH:i', 'Y-m-d\TH:i:s', 'Y-m-d H:i', 'Y-m-d H:i:s'] as $fmt) {
            try {
                $internment = Carbon::createFromFormat($fmt, $raw)->format('Y-m-d H:i:s');
                break;
            } catch (\Exception $e) {}
        }
        if (!$internment) {
            return response()->json(['message' => 'Invalid internment datetime.'], 422);
        }

        DB::transaction(function () use ($reservation, $v, $internment) {
            $appFirst = $v['applicant_first_name']  ?? $reservation->applicant_first_name;
            $appMid   = $v['applicant_middle_name'] ?? $reservation->applicant_middle_name;
            $appLast  = $v['applicant_last_name']   ?? $reservation->applicant_last_name;
            $appSuf   = $v['applicant_suffix']      ?? $reservation->applicant_suffix;

            $decFirst = $v['deceased_first_name']   ?? $reservation->deceased?->first_name;
            $decMid   = $v['deceased_middle_name']  ?? $reservation->deceased?->middle_name;
            $decLast  = $v['deceased_last_name']    ?? $reservation->deceased?->last_name;
            $decSuf   = $v['deceased_suffix']       ?? $reservation->deceased?->suffix;

            $reservation->update([
                'date_applied'             => Carbon::parse($v['date_applied'])->format('Y-m-d'),
                'internment_sched'         => $internment,

                'applicant_first_name'     => $appFirst,
                'applicant_middle_name'    => $appMid,
                'applicant_last_name'      => $appLast,
                'applicant_suffix'         => $appSuf,

                'applicant_address'        => $v['applicant_address'] ?? $reservation->applicant_address,
                'applicant_contact_no'     => $v['applicant_contact_no'] ?? $reservation->applicant_contact_no,
                'relationship_to_deceased' => $v['relationship_to_deceased'] ?? $reservation->relationship_to_deceased,

                'amount_as_per_ord'        => $v['amount_as_per_ord'] ?? $reservation->amount_as_per_ord,
                'funeral_service'          => $v['funeral_service'] ?? $reservation->funeral_service,
                'other_info'               => $v['other_info'] ?? $reservation->other_info,
            ]);

            if ($reservation->deceased) {
                $reservation->deceased->update([
                    'first_name'    => $decFirst,
                    'middle_name'   => $decMid,
                    'last_name'     => $decLast,
                    'suffix'        => $decSuf,
                    'date_of_birth' => Carbon::parse($v['date_of_birth'])->format('Y-m-d'),
                    'date_of_death' => Carbon::parse($v['date_of_death'])->format('Y-m-d'),
                ]);
            }
        });

        return $this->show($reservation->fresh());
    }

    public function list(Request $request)
    {
        $siteId  = (int) $request->query('burial_site_id', 0);
        $levelNo = (int) $request->query('level_no', 0);

        if (!$siteId || !$levelNo) {
            return response()->json(['data' => []]);
        }

        $rows = Reservation::query()
            ->active()
            ->forSiteLevel($siteId, $levelNo)
            ->with([
                'deceased:id,first_name,middle_name,last_name,suffix',
                'burialSite:id,name',
                'level:id,burial_site_id,level_no',
                'slot:id,grave_cell_id,slot_no',
                'slot.cell:id,level_id,row_no,col_no',
            ])
            ->latest('id')
            ->get([
                'id',
                'level_id',
                'burial_site_id',
                'deceased_id',
                'slot_id',
                'date_applied',
                'internment_sched',
                'applicant_first_name',
                'applicant_middle_name',
                'applicant_last_name',
                'applicant_suffix',
            ]);

        $data = $rows->map(function ($r) {
            $d = $r->deceased;
            $deceasedName = trim(implode(' ', array_filter([
                $d?->first_name,
                $d?->middle_name,
                $d?->last_name,
                $d?->suffix,
            ])));

            return [
                'id'               => $r->id,
                'date_applied'     => optional($r->date_applied)->format('Y-m-d'),

                'internment_sched' => optional($r->internment_sched)->format('Y-m-d H:i:s'),
                'applicant_name'   => $r->applicant_name,
                'deceased_name'    => $deceasedName,
                'burial_site'      => $r->burialSite?->name,
                'level_no'         => $r->level?->level_no,
                'location'         => $r->buried_at,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function export(Request $request)
    {
        $request->validate([
            'burial_site_id' => 'required|integer|exists:burial_sites,id',
            'level_no'       => 'required|integer|min:1',
        ]);

        $site  = BurialSite::findOrFail($request->burial_site_id);
        $level = (int) $request->level_no;
        $fname = 'cemetery_'.Str::slug($site->name, '_')."_level_{$level}.xlsx";

        return Excel::download(new ReservationExport($site->id, $level), $fname);
    }
}
