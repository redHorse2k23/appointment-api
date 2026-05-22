<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('owner', function ($user) {
            return $user->role === 'owner';
        });

        Gate::define('user', function ($user) {
            return $user->role === 'user';
        });
    }
}