<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        RateLimiter::for('login', function (Request $request): Limit {
            return Limit::perMinute(5)->by(
                'login:'.Str::lower((string) $request->string('email')).'|'.$request->ip()
            );
        });

        RateLimiter::for('register', function (Request $request): Limit {
            return Limit::perMinute(3)->by('register:'.$request->ip());
        });

        RateLimiter::for('authenticated', function (Request $request): Limit {
            return Limit::perMinute(60)->by('authenticated:'.$request->user()->getAuthIdentifier());
        });
    }
}
