<?php

namespace App\Http\Controllers;

use App\Models\Renewal;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\Reservation;
use App\Models\Exhumation;

class ReportController extends Controller
{
    public function Generate_report()
    {
        return view('Report.Generate_report');
    }

    public function print_report(Request $request)
    {
        $level = $request->input('level');
        $categories = Reservation::where('level_id', $level)->get();
        $pdf = PDF::loadView('Report.print_report', compact('categories', 'level'));
    }

    // BURIAL APPLICATION PERMIT (generic)
    public function Generate_Burial_Application_Permit()
    {
        $date = Carbon::now()->toFormattedDateString();
        $pdf = PDF::loadView('Permits.burial_application_permit', ['date' => $date])
                  ->setPaper('A4', 'Portrait');
        $pdf->setBasePath(public_path());
        return $pdf->stream();
    }

    // RENEWAL APPLICATION PERMIT (generic)
    public function Generate_Burial_Permit()
    {
        $date = Carbon::now()->toFormattedDateString();
        $pdf = PDF::loadView('Permits.burial_permit', ['date' => $date])
                  ->setPaper('A4', 'Portrait');
        $pdf->setBasePath(public_path());
        return $pdf->stream();
    }

    // EXHUMATION REQUEST PERMIT (generic)
    public function Generate_Exhumation_Permit()
    {
        $date = Carbon::now()->toFormattedDateString();
        $pdf = PDF::loadView('Permits.exhumation_permit', ['date' => $date])
                  ->setPaper('A4', 'Portrait');
        $pdf->setBasePath(public_path());
        return $pdf->stream();
    }



    private function ordinalUpper(int $n): string
    {
        $v = $n % 100;
        if ($v >= 11 && $v <= 13) return strtoupper($n.'th');
        $suffix = ['th','st','nd','rd','th','th','th','th','th','th'][$n % 10];
        return strtoupper($n.$suffix);
    }


    private function apartmentLevelOnly(?\App\Models\Slot $slot = null, ?\App\Models\Reservation $reservation = null): ?string
    {

        $level = $slot?->cell?->level;
        $apartmentName = $level?->apartment?->name;


        if (!$apartmentName && $reservation) {
            $level = $level ?: $reservation->level;
            $apartmentName = $level?->apartment?->name ?: $reservation->burialSite?->name;
        }


        if (!$level || !$apartmentName) return null;

        $ord = $this->ordinalUpper((int) $level->level_no);

        return strtoupper(trim($apartmentName.' '.$ord.' LEVEL'));
    }


    private function ordinal(int $n): string
    {
        $v = $n % 100;
        if ($v >= 11 && $v <= 13) return $n.'th';
        return $n . (['th','st','nd','rd','th','th','th','th','th','th'][$n % 10]);
    }

    private function formatLocation(?\App\Models\Slot $s): ?string
    {
        if (! $s || ! $s->cell || ! $s->cell->level) return null;
        $cell  = $s->cell;
        $level = $cell->level;
        $site  = $level->apartment;

        return sprintf(
            '%s, %s Level Column: %s Row: %s Slot: %s',
            $site?->name ?? '—',
            $this->ordinal((int)$level->level_no),
            $cell->col_no,
            $cell->row_no,
            $s->slot_no
        );
    }


    private function formatGraveDiggers(\App\Models\Reservation $r): string
    {
        $names = $r->graveDiggersMany
            ? $r->graveDiggersMany->pluck('name')->filter()->map(fn($n) => trim($n))->filter()->values()->all()
            : [];

        if (count($names) === 0) {
            $single = $r->grave_diggers?->name;
            if ($single) $names = [trim($single)];
        }

        if (count($names) === 0) return '—';

        return implode(' / ', $names);
    }



    public function generateRenewalPermit(Renewal $renewal)
    {
        $renewal->loadMissing('deceased', 'slot.cell.level.apartment', 'verifier');

        $dec   = $renewal->deceased;
        $cell  = $renewal->slot?->cell;
        $level = $cell?->level;
        $apt   = $level?->apartment;
        $aptName = $apt?->name ?? '—';


        $location = $this->apartmentLevelOnly($renewal->slot)
                 ?: (($level && $aptName !== '—')
                        ? strtoupper($aptName.' '.$this->ordinalUpper((int)$level->level_no).' LEVEL')
                        : '—');

        $dateApplied  = $renewal->date_applied?->format('F d, Y') ?? '—';
        $dod          = $dec?->date_of_death ? Carbon::parse($dec->date_of_death)->format('F d, Y') : '—';
        $periodLabel  = ($renewal->renewal_start && $renewal->renewal_end)
            ? $renewal->renewal_start->year.'-'.$renewal->renewal_end->year
            : '—';
        $feeNumeric   = is_null($renewal->amount_as_per_ord) ? null : number_format((float)$renewal->amount_as_per_ord, 2);
        $verifierName = $renewal->verifier?->name_of_verifier ?? '—';

        $pdf = Pdf::loadView('Permits.burial_permit', compact(
            'renewal', 'dec', 'aptName', 'location',
            'dateApplied', 'dod', 'periodLabel', 'feeNumeric', 'verifierName'
        ))->setPaper('A4', 'portrait');

        $pdf->setBasePath(public_path());
        return $pdf->stream('renewal-permit-'.$renewal->id.'.pdf');
    }

    public function exhumationPermit(Exhumation $exhumation)
    {
        $exhumation->loadMissing([
            'reservation.deceased',
            'reservation.slot.cell.level.apartment',
            'fromSlot.cell.level.apartment',
            'toSlot.cell.level.apartment',
            'verifier',
        ]);

        $dec = $exhumation->reservation?->deceased;

        $burialLocation = $this->formatLocation($exhumation->fromSlot)
            ?: $this->formatLocation($exhumation->reservation?->slot)
            ?: ($exhumation->current_location ?: '—');

        $transferDestination = $exhumation->to_slot_id
            ? ($this->formatLocation($exhumation->toSlot) ?? '—')
            : ($exhumation->current_location ?: '—');

        $deceasedName = $dec?->name_of_deceased ?? '—';
        $deathDate    = $dec?->date_of_death ? Carbon::parse($dec->date_of_death)->format('F d, Y') : '—';

        $dateIssued   = $exhumation->or_issued_at
            ? Carbon::parse($exhumation->or_issued_at)->format('F d, Y')
            : ($exhumation->date_applied ? Carbon::parse($exhumation->date_applied)->format('F d, Y') : now()->format('F d, Y'));

        $feeNumeric   = is_null($exhumation->amount_as_per_ord) ? '—' : number_format((float)$exhumation->amount_as_per_ord, 2);
        $verifierName = $exhumation->verifier?->name_of_verifier ?? '—';

        $pdf = Pdf::loadView('Permits.exhumation_permit', [
            'exhumation'          => $exhumation,
            'deceasedName'        => $deceasedName,
            'deathDate'           => $deathDate,
            'burialLocation'      => $burialLocation,
            'transferDestination' => $transferDestination,
            'dateIssued'          => $dateIssued,
            'feeNumeric'          => $feeNumeric,
            'verifierName'        => $verifierName,
        ])->setPaper('A4', 'portrait');

        $pdf->setBasePath(public_path());
        return $pdf->stream('exhumation-permit-'.$exhumation->id.'.pdf');
    }

    public function burialApplication(Request $request, Reservation $reservation)
    {

        $reservation->load([
            'deceased:id,first_name,middle_name,last_name,suffix,address_before_death,sex,date_of_birth,date_of_death',
            'burialSite:id,name',
            'level:id,level_no,burial_site_id',
            'slot:id,grave_cell_id,slot_no',
            'grave_diggers:id,name',
            'graveDiggersMany:id,name',
            'verifiers:id,name_of_verifier,position',
        ]);
        $reservation->loadMissing('slot.cell.level.apartment');

        $applicant = trim(collect([
            $reservation->applicant_first_name,
            $reservation->applicant_middle_name,
            $reservation->applicant_last_name,
            $reservation->applicant_suffix,
        ])->filter()->implode(' '));

        $d = $reservation->deceased;
        $deceasedName = trim(collect([
            $d?->first_name, $d?->middle_name, $d?->last_name, $d?->suffix,
        ])->filter()->implode(' '));


        $graveDiggersFormatted = $this->formatGraveDiggers($reservation);

        $burialLocation = $this->apartmentLevelOnly($reservation->slot, $reservation)
                         ?: '—';

        $data = [
            'r'                 => $reservation,
            'date_applied'      => ($reservation->date_applied ? substr((string)$reservation->date_applied, 0, 10) : '—'),
            'applicant_name'    => $applicant ?: '—',
            'applicant_addr'    => $reservation->applicant_address ?: '—',
            'applicant_contact' => $reservation->applicant_contact_no ?: '—',
            'relationship'      => $reservation->relationship_to_deceased ?: '—',
            'deceased_name'     => $deceasedName ?: '—',
            'deceased_addr'     => $d?->address_before_death ?: '—',
            'dob'               => $d?->dob_ymd ?: '—',
            'dod'               => $d?->dod_ymd ?: '—',


            'burial_site'       => $reservation->location_or_apt_level,

            'amount'            => $reservation->amount_as_per_ord ?: '—',
            'funeral_service'   => $reservation->funeral_service ?: '—',
            'grave_digger'      => $graveDiggersFormatted,
            'internment'        => ($reservation->internment_sched ?: '—'),
            'other_info'        => $reservation->other_info ?: '—',
            'verifier'          => $reservation->verifiers?->name_of_verifier ?: '—',


            'burialLocation'    => $burialLocation,
        ];

        $dir = storage_path('app/permits/burial_application');
        if (!is_dir($dir)) @mkdir($dir, 0755, true);

        $filename = "burial_application_{$reservation->id}.pdf";
        $fullpath = $dir . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($fullpath) && !$request->boolean('force')) {
            return response()->file($fullpath, ['Content-Type' => 'application/pdf']);
        }

        $pdf = Pdf::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
            ])
            ->loadView('Permits.burial_application_permit', $data)
            ->setPaper('A4', 'portrait');

        file_put_contents($fullpath, $pdf->output());

        return response()->file($fullpath, ['Content-Type' => 'application/pdf']);
    }
}
