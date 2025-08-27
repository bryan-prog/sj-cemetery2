<?php

namespace App\Http\Controllers;

use App\Models\BurialSite;
use App\Models\Level;
use Illuminate\Http\Request;
use App\Models\{Family, Reservation};

class LookupController extends Controller
{
    public function index()
    {
        return BurialSite::select('id','name')->orderBy('name')->get();
    }

    public function levels($id)
    {
        return Level::where('burial_site_id', $id)
            ->orderBy('level_no')
            ->get(['id','level_no']);
    }


    public function searchFamilies(Request $request)
    {

        if ($id = $request->query('id')) {
            $fam = Family::select(
                    'id',
                    'first_name','middle_name','last_name','suffix',
                    'contact_no','address'
                )
                ->where('id', $id)
                ->first();

            if (!$fam) return response()->json([]);
            $row = $this->decorateFamilyRow($fam, null);
            return response()->json([$row]);
        }

        $q = trim((string)$request->query('q', ''));
        if (mb_strlen($q) < 2) return response()->json([]);


        $families = Family::query()
            ->select('id','first_name','middle_name','last_name','suffix','contact_no','address')
            ->where(function($w) use ($q) {
                $w->where('last_name', 'like', "%{$q}%")
                  ->orWhere('first_name', 'like', "%{$q}%")
                  ->orWhere('middle_name', 'like', "%{$q}%")
                  ->orWhere('address', 'like', "%{$q}%");
            })
            ->limit(25)
            ->get();

        $familyIdsByApplicant = Reservation::query()
            ->whereNotNull('family_id')
            ->whereRaw("
                UPPER(
                    LTRIM(RTRIM(
                        CONCAT(
                            COALESCE(applicant_first_name, ''), ' ',
                            COALESCE(applicant_middle_name, ''), ' ',
                            COALESCE(applicant_last_name, ''), ' ',
                            COALESCE(applicant_suffix, '')
                        )
                    ))
                ) LIKE ?
            ", ['%' . mb_strtoupper($q) . '%'])
            ->distinct()
            ->pluck('family_id');

        $families2 = Family::query()
            ->select('id','first_name','middle_name','last_name','suffix','contact_no','address')
            ->whereIn('id', $familyIdsByApplicant)
            ->limit(25)
            ->get();

        $merged = $families->concat($families2)->unique('id')->values();

        $rows = $merged->map(fn($f) => $this->decorateFamilyRow($f, $q))->values();

        return response()->json($rows);
    }


    public function storeFamily(Request $request)
    {
        $v = $request->validate([
            'last_name'   => 'required|string|max:255',
            'first_name'  => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'suffix'      => 'nullable|string|max:50',
            'contact_no'  => 'nullable|string|max:50',
            'address'     => 'nullable|string|max:255',
        ]);

        $fam = Family::create([
            'first_name'  => $v['first_name']  ?? null,
            'middle_name' => $v['middle_name'] ?? null,
            'last_name'   => mb_strtoupper($v['last_name']),
            'suffix'      => $v['suffix']      ?? null,
            'contact_no'  => $v['contact_no']  ?? null,
            'address'     => $v['address']     ?? null,

        ]);


        return response()->json([
            'id'          => $fam->id,
            'first_name'  => $fam->first_name,
            'middle_name' => $fam->middle_name,
            'last_name'   => $fam->last_name,
            'suffix'      => $fam->suffix,
            'contact_no'  => $fam->contact_no,
            'address'     => $fam->address,
        ], 201);
    }

    private function decorateFamilyRow(Family $f, ?string $needle = null): array
    {

        $activeCount = Reservation::active()->where('family_id', $f->id)->count();


        $lastBurial = Reservation::where('family_id', $f->id)
            ->latest('internment_sched')
            ->value('internment_sched');

        $latestForNeedle = null;
        if ($needle && mb_strlen($needle) >= 2) {
            $latestForNeedle = Reservation::with(['level.apartment'])
                ->where('family_id', $f->id)
                ->whereRaw("
                    UPPER(
                        LTRIM(RTRIM(
                            CONCAT(
                                COALESCE(applicant_first_name, ''), ' ',
                                COALESCE(applicant_middle_name, ''), ' ',
                                COALESCE(applicant_last_name, ''), ' ',
                                COALESCE(applicant_suffix, '')
                            )
                        ))
                    ) LIKE ?
                ", ['%' . mb_strtoupper($needle) . '%'])
                ->latest('internment_sched')
                ->first();
        }

        $latestAny = $latestForNeedle ?: Reservation::with(['level.apartment'])
            ->where('family_id', $f->id)
            ->latest('internment_sched')
            ->first();

        $aptName = optional(optional($latestAny)->level)->apartment->name ?? null;
        $lvlNo   = optional($latestAny->level ?? null)->level_no;
        $lotLoc  = ($aptName && $lvlNo) ? ('APARTMENT: ' . strtoupper($aptName) . ' LEVEL: ' . $lvlNo) : null;

        $defaultSiteId  = optional($latestAny)->burial_site_id;
        $defaultLevelId = optional($latestAny)->level_id;

        return [
            'id'               => $f->id,
            'first_name'       => $f->first_name,
            'middle_name'      => $f->middle_name,
            'last_name'        => $f->last_name,
            'suffix'           => $f->suffix,
            'contact_no'       => $f->contact_no,
            'address'          => $f->address,
            'active_graves'    => $activeCount,
            'last_burial_at'   => $lastBurial ? \Carbon\Carbon::parse($lastBurial)->format('Y-m-d H:i') : null,
            'lot_location'     => $lotLoc,
            'default_site_id'  => $defaultSiteId,
            'default_level_id' => $defaultLevelId,
        ];
    }


    public function showFamily(Family $family)
{

    return response()->json([
        'id'          => $family->id,
        'first_name'  => $family->first_name,
        'middle_name' => $family->middle_name,
        'last_name'   => $family->last_name,
        'suffix'      => $family->suffix,
        'contact_no'  => $family->contact_no,
        'address'     => $family->address,
    ]);
}

public function updateFamily(Request $request, Family $family)
{
    $v = $request->validate([
        'last_name'   => 'required|string|max:255',
        'first_name'  => 'nullable|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'suffix'      => 'nullable|string|max:50',
        'contact_no'  => 'nullable|string|max:50',
        'address'     => 'nullable|string|max:255',
    ]);


    $family->last_name   = mb_strtoupper($v['last_name']);
    $family->first_name  = $v['first_name']  ?? null;
    $family->middle_name = $v['middle_name'] ?? null;
    $family->suffix      = $v['suffix']      ?? null;
    $family->contact_no  = $v['contact_no']  ?? null;
    $family->address     = $v['address']     ?? null;
    $family->save();


    return response()->json([
        'ok'   => true,
        'id'   => $family->id,
        'msg'  => 'Family updated',
    ]);
}
}
