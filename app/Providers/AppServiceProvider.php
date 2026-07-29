<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        config([
            'jwt.ttl' => config('auth_tokens.access_ttl'),
            'jwt.refresh_ttl' => config('auth_tokens.refresh_ttl'),
            'jwt.blacklist_enabled' => config('auth_tokens.blacklist_enabled'),
        ]);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
