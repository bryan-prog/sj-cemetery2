<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use App\Models\GraveDiggers;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use App\Models\Graves;


class HomeController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth');
    }

    public function homepage()
    {
        return view('homepage');
    }



    //CEMETERY ALL DATA
    public function cemetery_data()
    {
        return view('cemetery_data');
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



}
