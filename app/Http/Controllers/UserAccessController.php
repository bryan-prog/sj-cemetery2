<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    public function index()
    {
        $users = User::orderBy('lname')->orderBy('fname')->get();
        return view('user_access.index', compact('users'));
    }

    /**
     * Return one user's current access flags (for the modal).
     * Route: GET /user-access/{user}  name:user-access.show
     */
    public function show(User $user)
    {
        // Route is already protected by: ['auth','can:manage-user-access']
        return response()->json([
            'id'                  => $user->id,
            'name'                => "{$user->lname}, {$user->fname}",
            'permission'          => $user->permission,
            'can_approve_deny'    => (bool) $user->can_approve_deny,
            'can_view_actionlogs' => (bool) $user->can_view_actionlogs,
            'can_edit_permits'    => (bool) $user->can_edit_permits,
            'can_print_permits'   => (bool) $user->can_print_permits,
        ]);
    }

    public function update(Request $request, User $user)
    {

        $this->authorize('manage-user-access');


        if (strtolower((string) $user->permission) === 'super admin') {
            return back()->with('success', 'Permissions for Super Admin are locked.');
        }

        $user->fill([
            'can_approve_deny'    => $request->boolean('can_approve_deny'),
            'can_view_actionlogs' => $request->boolean('can_view_actionlogs'),
            'can_edit_permits'    => $request->boolean('can_edit_permits'),
            'can_print_permits'   => $request->boolean('can_print_permits'),
        ])->save();

        return back()->with('success', 'Access updated for '.$user->fname.' '.$user->lname.'.');
    }
}
