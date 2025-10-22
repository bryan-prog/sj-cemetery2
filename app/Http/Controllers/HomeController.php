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
use App\Models\Deceased;
use Illuminate\Support\Facades\Hash;

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
        $female_dead = Deceased::where('sex','female')->count();
        $male_dead = Deceased::where('sex','male')->count();

        $death_jan= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '01')->count();
        $death_feb= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '02')->count();
        $death_mar= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '03')->count();
        $death_apr= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '04')->count();
        $death_may= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '05')->count();
        $death_jun= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '06')->count();
        $death_jul= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '07')->count();
        $death_aug= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '08')->count();
        $death_sep= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '09')->count();
        $death_oct= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '10')->count();
        $death_nov= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '11')->count();
        $death_dec= Deceased::whereYear('date_of_death', Carbon::now()->year)->whereMonth('date_of_death', '12')->count();


        $ren_jan = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '01')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_feb = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '02')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_mar = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '03')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_apr = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '04')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_may = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '05')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_jun = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '06')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_jul = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '07')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_aug = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '08')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_sep = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '09')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_oct = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '10')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_nov = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '11')->whereRaw('LOWER(status)=?',['approved'])->count();
        $ren_dec = Renewal::whereYear('date_applied', Carbon::now()->year)->whereMonth('date_applied', '12')->whereRaw('LOWER(status)=?',['approved'])->count();

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
            'levelProgress',
            'female_dead','male_dead',
            'death_jan','death_feb','death_mar','death_apr','death_may','death_jun','death_jul','death_aug','death_sep','death_oct','death_nov','death_dec',
            'ren_jan','ren_feb','ren_mar','ren_apr','ren_may','ren_jun','ren_jul','ren_aug','ren_sep','ren_oct','ren_nov','ren_dec',
        ));
    }


    public function cemetery_data()
    {
        $apartments = BurialSite::orderBy('name')->get(['id','name']);
        return view('cemetery_data', compact('apartments'));
    }


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
  public function changePassword(Request $request)
{
    $request->validate([
        'info_id' => 'required|exists:users,id',
        'change_password' => 'required|min:6',
    ]);

    $user = User::find($request->info_id);
    $user->password = Hash::make($request->change_password);
    $user->save();

    return redirect()->back()->with('success', 'Password successfully updated!');
}

    public function logs()
    {
        return view('logs');
    }

    public function order_of_payment()
    {
        return view('order_of_payment');
    }
}
