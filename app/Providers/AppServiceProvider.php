<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::define('manage-menu', function (User $user) {
            return in_array($user->role, ['admin', 'manajer']);
        });

        Gate::define('delete-menu', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
