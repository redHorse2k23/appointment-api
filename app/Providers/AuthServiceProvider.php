<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\User;
use App\Policies\BookingPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Booking::class => BookingPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();

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