<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

         Gate::before(function (User $user, $ability) {
        return $user->permission === 'Super Admin' ? true : null;
    });

    Gate::define('manage-user-access', fn(User $user) =>
        in_array($user->permission, ['Super Admin'])
    );

    Gate::define('approve-deny', fn(User $user) => $user->can_approve_deny);
    Gate::define('view-actionlogs', fn(User $user) => $user->can_view_actionlogs);
    Gate::define('edit-permits', fn(User $user) => $user->can_edit_permits);
    Gate::define('print-permits', fn(User $user) => $user->can_print_permits);


    }
}
