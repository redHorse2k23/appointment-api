<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::define('admin-only', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('manager-only', function ($user) {
            return in_array($user->role, ['manager', 'admin']);
        });
    }
}