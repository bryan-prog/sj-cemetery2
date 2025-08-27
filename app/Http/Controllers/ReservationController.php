<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Carbon;
use App\Exports\ReservationExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use App\Models\BurialSite;
use Illuminate\Support\Str;

class ReservationController extends Controller
{

 public function list(Request $request)
    {
        $siteId  = (int) $request->input('burial_site_id');
        $levelNo = (int) $request->input('level_no');

        if (!$siteId || !$levelNo) {
            return response()->json(['data' => []]);
        }

        $rows = Reservation::query()
            ->active()
            ->forSiteLevel($siteId, $levelNo)
            ->with([
                'deceased',
                'burialSite:id,name',
                'level:id,burial_site_id,level_no',
                'slot.cell.level.apartment',
                'latestApprovedRenewal',
            ])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($r) {
                $dob = optional($r->deceased)->date_of_birth;
                $dod = optional($r->deceased)->date_of_death;
                $s   = $r->renewal_start;
                $e   = $r->renewal_end;

                return [
                    'buried_at'      => $r->buried_at,
                    'name_of_deceased'  => optional($r->deceased)->name_of_deceased,
                    'sex'            => optional($r->deceased)->sex,
                    'date_of_birth'  => $dob ? Carbon::parse($dob)->format('Y-m-d') : null,
                    'date_of_death'  => $dod ? Carbon::parse($dod)->format('Y-m-d') : null,
                    'renewal_period' => ($s && $e)
                        ? Carbon::parse($s)->format('Y-m-d').' - '.Carbon::parse($e)->format('Y-m-d')
                        : '—',
                    'contact_person' => $r->applicant_name,
                ];
            });

        return response()->json(['data' => $rows]);



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
