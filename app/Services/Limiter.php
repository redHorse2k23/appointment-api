<?php

namespace App\Services;

use Illuminate\Support\Facades\RateLimiter;

class Limiter
{
    public static function handle($key, $maxAttempts = 3, $decaySeconds = 60)
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return [
                'allowed' => false,
                'seconds' => RateLimiter::availableIn($key),
            ];
        }

        RateLimiter::hit($key, $decaySeconds);

        return [
            'allowed' => true,
        ];
    }
}