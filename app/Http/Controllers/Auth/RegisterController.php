<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('auth');
    }


    protected function validator(array $data)
    {
        return Validator::make($data, [
             'lname' => 'required|string|max:255',
             'fname' => 'required|string|max:255',
             'mname' => 'nullable|string|max:255',
             'suffix' => 'nullable|string|max:255',
             'permission' => 'required|string|max:255',
             'designation' => 'required|string|max:255',
             'username' => 'required|string|max:255|unique:users',
             'password' => 'required|string|min:6|confirmed',


        ]);
    }


    protected function create(array $data)
    {
       return User::create([
            'lname'     => $data['name'],
            'fname' => $data['username'],
            'mname' => $data['mname'],
            'suffix' => $data['suffix'],
            'designation' => $data['designation'],
            'permission' => $data['permission'],
            'username' => $data['username'],
            'password' => bcrypt($data['password']),
            'active' => 1
        ]);

        return $user;



        return Redirect::action('Auth/RegisterController@register')
            ->with('message', 'SUCCESSFULLY SAVED USER DATA!');
    }

}
