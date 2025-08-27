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
    //

    public function Generate_report()
    {
        return view('Report.Generate_report');
    }

    public function print_report(Request $request)
    {

          $level = $request->input('level');

        // Fetch categories or data based on the selected level (or any other criteria)
        // Example: Assume you have a `Category` model, which can filter by `level`
        $categories = Reservation::where('level_id', $level)->get();

        // Return the PDF with data
        $pdf = PDF::loadView('Report.print_report', compact('categories', 'level'));
    }

    //BURIAL APPLICATION PERMIT
    public function Generate_Burial_Application_Permit()
    {
        $date = Carbon::now()->toFormattedDateString();
        $pdf = PDF::loadView('Permits.burial_application_permit', array('date' => $date))
                ->setPaper('A4', 'Portrait');
        $pdf->setBasePath(public_path());
        return $pdf->stream();
    }

    //RENEWAL APPLICATION PERMIT
    public function Generate_Burial_Permit()
    {
        $date = Carbon::now()->toFormattedDateString();
        $pdf = PDF::loadView('Permits.burial_permit', array('date' => $date))
                ->setPaper('A4', 'Portrait');
        $pdf->setBasePath(public_path());
        return $pdf->stream();
    }

    //EXHUMATION REQUEST PERMIT
    public function Generate_Exhumation_Permit()
    {
        $date = Carbon::now()->toFormattedDateString();
        $pdf = PDF::loadView('Permits.exhumation_permit', array('date' => $date))
                ->setPaper('A4', 'Portrait');
        $pdf->setBasePath(public_path());
        return $pdf->stream();
    }

//// Convert function sa specific format
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

    public function generateRenewalPermit(Renewal $renewal)
    {

        $renewal->loadMissing('deceased', 'slot.cell.level.apartment', 'verifier');

        $dec      = $renewal->deceased;
        $cell     = $renewal->slot?->cell;
        $level    = $cell?->level;
        $apt      = $level?->apartment;
        $aptName  = $apt?->name ?? '—';
         $location     = $renewal->slot
        ? ($this->formatLocation($renewal->slot) ?? '—')
        : ($renewal->buried_at ?? '—');

        $dateApplied = $renewal->date_applied?->format('F d, Y') ?? '—';
        $dod         = $dec?->date_of_death ? \Carbon\Carbon::parse($dec->date_of_death)->format('F d, Y') : '—';
       $periodLabel = ($renewal->renewal_start && $renewal->renewal_end)
       ? $renewal->renewal_start->year.'-'.$renewal->renewal_end->year
       : '—';

       $feeNumeric = is_null($renewal->amount_as_per_ord) ? null : number_format((float)$renewal->amount_as_per_ord, 2);
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

    public function generateBurialPermit(Burial $burial)
    {

       $burial->loadMissing(  
        'reservation.deceased',
        'reservation.slot.cell.level.apartment',
        'fromSlot.cell.level.apartment',
        'toSlot.cell.level.apartment',
        'verifier',);

  
        $dec      = $burial->reservation?->deceased;
        $cell     = $burial->slot?->cell;
        $level    = $cell?->level;
        $apt      = $level?->apartment;
        $aptName  = $apt?->name ?? '—';
        $location     = $burial->slot
        ? ($this->formatLocation($burial->slot) ?? '—')
        : ($burial->buried_at ?? '—');

     
    $burialLocation = $this->formatLocation($burial->fromSlot)
        ?: $this->formatLocation($burial->reservation?->slot)
        ?: ($burial->current_location ?: '—');


    $transferDestination = $burial->to_slot_id
        ? ($this->formatLocation($burial->toSlot) ?? '—')
        : ($burial->current_location ?: '—');

    $deceasedName = $dec?->name_of_deceased ?? '—';
    $deathDate    = $dec?->date_of_death ? Carbon::parse($dec->date_of_death)->format('F d, Y') : '—';
    $deathBirth    = $dec?->date_of_death ? Carbon::parse($dec->date_of_birth)->format('F d, Y') : '—';
 

    $dateIssued   = $burial->or_issued_at
        ? Carbon::parse($exhumation->or_issued_at)->format('F d, Y')
        : ($exhumation->date_applied ? Carbon::parse($exhumation->date_applied)->format('F d, Y') : now()->format('F d, Y'));

    $feeNumeric   = is_null($burial>amount_as_per_ord) ? '—' : number_format((float)$burial->amount_as_per_ord, 2);
    $verifierName = $burial->verifier?->name_of_verifier ?? '—';


        $pdf = Pdf::loadView('Permits.burial_application_permit', [
        'exhumation'          => $exhumation,
        'deceasedName'        => $deceasedName,
        'deathDate'           => $deathDate,
        'deathBirth'           => $deathBirth,
        'burialLocation'      => $burialLocation,
        'transferDestination' => $transferDestination,
        'dateIssued'          => $dateIssued,
        'feeNumeric'          => $feeNumeric,
        'verifierName'        => $verifierName,
       ])->setPaper('A4', 'portrait');


        $pdf->setBasePath(public_path());
        return $pdf->stream('burial-permit-'.$burial->id.'.pdf');
    }

}
