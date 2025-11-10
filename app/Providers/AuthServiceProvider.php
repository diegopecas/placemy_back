<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Auth\Services\AuthService;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register Auth domain services.
     */
    public function register(): void
    {
        $this->app->bind(AuthService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
