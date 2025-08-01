<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pdf;
use Carbon\Carbon;
use App\Models\Reservation;

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


    


}
