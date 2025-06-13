<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Pdf;
use Carbon\Carbon;

class ReportController extends Controller
{
    //

    public function Generate_report()
    {
        return view('Report.Generate_report');
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
}
