<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\User;

class RegisterApiController extends Controller
{
    /**
     * POST /api/auth/register
     *
     * Creates a user that matches the current SQL-Server table structure
     * and returns a plain-text Sanctum token plus a success message.
     */
    public function register(Request $request)
    {
        $data = $request->validate([

            'lname'   => ['required', 'string', 'max:255'],
            'fname'   => ['required', 'string', 'max:255'],
            'mname'   => ['nullable', 'string', 'max:255'],
            'suffix'  => ['nullable', 'string', 'max:50'],


            'designation' => ['required', 'string', 'max:255'],
            'permission'  => ['required', 'string', 'max:255'],

            'username' => ['required', 'string', 'max:50', Rule::unique('users')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        $user = User::create([
            'lname'       => $data['lname'],
            'fname'       => $data['fname'],
            'mname'       => $data['mname']      ?? null,
            'suffix'      => $data['suffix']     ?? null,
            'designation' => $data['designation'],
            'permission'  => $data['permission'],
            'username'    => $data['username'],
            'password'    => bcrypt($data['password']),
            'active'      => 1,

        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully.',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }
}
