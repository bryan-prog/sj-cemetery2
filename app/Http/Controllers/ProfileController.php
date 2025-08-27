<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    //
    
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'fname'        => 'required|string|max:255',
            'mname'        => 'nullable|string|max:255',
            'lname'        => 'required|string|max:255',
            'suffix'       => 'nullable|string|max:50',
            'designation'  => 'required|string|max:255',
            'username'     => 'required|string|max:255|unique:users,username,' . $user->id,
            'password'     => 'nullable|string|min:8|confirmed',
        ]);

        // Update user fields
        $user->fname = $validated['fname'];
        $user->mname = $validated['mname'] ?? null;
        $user->lname = $validated['lname'];
        $user->suffix = $validated['suffix'] ?? null;
        $user->designation = $validated['designation'];
        $user->username = $validated['username'];

        // Update password only if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }
}

