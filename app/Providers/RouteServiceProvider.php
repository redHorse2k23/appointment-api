<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public const HOME = '/home';

    protected $namespace = 'App\Http\Controllers';

    public function boot()
    {
        $this->configureRateLimiting();

       $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware(['api', 'auth:sanctum'])
                ->group(function () {

                    Route::prefix('admin')
                        ->namespace($this->namespace . '\Admin')
                        ->middleware('role:admin')
                        ->group(base_path('routes/admin-api.php'));

                    Route::prefix('owner')
                        ->namespace($this->namespace . '\Owner')
                        ->middleware('role:owner')
                        ->group(base_path('routes/owner-api.php'));

                    Route::prefix('user')
                        ->namespace($this->namespace)
                        ->middleware('role:user')
                        ->group(base_path('routes/user-api.php'));
                });

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
