<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('register', function (Request $request): Limit {
            return Limit::perMinute(6)->by((string) $request->ip());
        });

        RateLimiter::for('password-reset-link', function (Request $request): Limit {
            $email = (string) $request->input('email', 'unknown');

            return Limit::perMinute(4)->by($request->ip().'|'.strtolower($email));
        });

        RateLimiter::for('oauth-redirect', function (Request $request): Limit {
            return Limit::perMinute(20)->by((string) $request->ip());
        });

        RateLimiter::for('oauth-callback', function (Request $request): Limit {
            return Limit::perMinute(20)->by((string) $request->ip());
        });

        RateLimiter::for('oauth-profile', function (Request $request): Limit {
            return Limit::perMinute(10)->by((string) ($request->user()?->id ?? $request->ip()));
        });
    }
}
