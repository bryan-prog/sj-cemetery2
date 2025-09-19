<?php

namespace App\Http\Controllers;

use App\Models\Exhumation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\GraveDiggers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Models\Graves;
use App\Models\Renewal;
use App\Models\Reservation;
use App\Models\BurialSite;
use App\Models\Slot;
use App\Models\Level;






class HomeController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
    }


public function homepage()
{
    $reservationTotal  = Reservation::active()->count();
    $renewalPending    = Renewal::whereRaw('LOWER(status) = ?', ['pending'])->count();
    $exhumationPending = Exhumation::whereRaw('LOWER(status) = ?', ['pending'])->count();

    $overallTotal = $reservationTotal + $renewalPending + $exhumationPending;

    $renewals = Renewal::with([
                    'slot.cell.level.apartment',
                    'deceased',
                ])
                ->where('status', 'pending')
                ->latest('id')
                ->paginate(10);


    $restos = BurialSite::where('name', 'Restos')
                ->with('levels:id,burial_site_id,level_no')
                ->first();


    $levelProgress = collect(range(1, 7))->mapWithKeys(function ($lvl) {
        return [$lvl => ['percent' => 0, 'busy' => 0, 'total' => 0, 'level_id' => null]];
    })->toArray();

    if ($restos) {
        foreach ($restos->levels as $level) {

            $busyStatuses = ['occupied', 'reserved', 'renewal_pending', 'exhumation_pending', 'for_penalty'];

            $total = Slot::whereHas('cell', fn($q) => $q->where('level_id', $level->id))
                         ->count();

            $busy  = Slot::whereHas('cell', fn($q) => $q->where('level_id', $level->id))
                         ->whereIn('status', $busyStatuses)
                         ->count();

            $levelProgress[$level->level_no] = [
                'percent'  => $total ? (int) round(($busy / $total) * 100) : 0,
                'busy'     => $busy,
                'total'    => $total,
                'level_id' => $level->id,
            ];
        }
    }

    return view('homepage', compact(
        'reservationTotal',
        'renewalPending',
        'exhumationPending',
        'overallTotal',
        'renewals',
        'levelProgress'
    ));
}



    //CEMETERY ALL DATA
public function cemetery_data()
{
    $apartments = BurialSite::orderBy('name')->get(['id','name']);
    return view('cemetery_data', compact('apartments'));
}




    //USERS
    public function list_of_users()
    {
          if (Auth::user()->permission == 'Super Admin') {
            $users = User::all();
            return view('list_of_users', compact('users'));
        } else if (Auth::user()->permission == 'Admin') {
            $users = User::where('permission', 'End User')->get();
            return view('list_of_users', compact('users'));
        } else {
            abort(403);
        }
    }

    public function user_details($id){
         $user = User::find($id);

         return Response::json($user);

         $user=User::all();
    }


    public function change_user_info(Request $request){

          $user = User::where('id', $request->info_id)->first();

          $user->lname = $request->last_name;
          $user->fname = $request->first_name;
          $user->mname = $request->middle_name;
          $user->suffix = $request->suffix;
          $user->designation = $request->designation;
          $user->permission = $request->permission;
          $user->active = $request->active;

          $user->save();




        return back()->with('message', "Successfully changed user details!");


    }

    //BURIAL APPLICATION

    //EXHUMATION
      public function exhumation_application_form()
    {
        return view('exhumation_application_form');
    }

    //test
      public function test()
    {
        return view('test');
    }

      public function my_profile()
    {
        return view('my_profile');
    }


    public function test_list_of_users()
    {
         $User=User::all();

        return DataTables::of($User)
            ->setRowId('id')
            ->make(true);

    }


    public function Test_edit_user(Request $request)
    {
        User::where('id', $request->info_id)
            ->update([
                'lname' => $request->last_name,
                'fname' => $request->first_name,
                'mname' => $request->middle_name,
                'suffix' => $request->suffix,
                'designation' => $request->designation,
                'permission' => $request->permission,
                'active ' => $request->status,
                'updated_at' => Carbon::now(),

            ]);

        return back()->with('message', 'Successfully updated user information!');
    }

    public function logs()
    {
        return view('logs');
    }

}
