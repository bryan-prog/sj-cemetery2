<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReservationResource;
use App\Http\Resources\RenewalResource;
use App\Models\Exhumation;
use App\Models\Renewal;
use App\Models\Reservation;
use Illuminate\Http\Request;




class RenewalPermitApiController extends Controller
{
    public function show(Renewal $renewal)
{
    return response()->json($renewal);
}


public function list_of_renewals(){

    $renewals = Renewal::with(['slot'])->get();

    return response()->json($renewals);

}

public function list_of_reservations()
{

    $reservations = Reservation::with(['deceased'])->get();

    return response()->json($reservations);
}


public function list_of_exhumations(){
    $exhumations = Exhumation::all();

    return response()->json($exhumations);
}


  public function index()
    {

        $reservations = Reservation::with([
                'deceased:id,name_of_deceased,sex,date_of_birth,date_of_death',
                'slot.cell.level.apartment',
                'latestApprovedRenewal',
            ])
            ->latest('id')
            ->get();

        return ReservationResource::collection($reservations);
    }

      public function listOfRenewals()
    {
        $renewals = Renewal::with([
                'slot.cell.level.apartment',   // for buried_at
                'reservation.deceased',
                'verifier',
            ])
            ->latest('id')
            ->get();

        return RenewalResource::collection($renewals);
    }




}
