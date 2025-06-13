<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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

    //LEVELS
    public function Level_1()
    {
        return view('Level.level_1');
    }
    public function Level_2()
    {
        return view('Level.level_2');
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
    public function burial_application_form()
    {
        
        return view('burial_application_form');
    }
}
