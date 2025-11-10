<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Shared\Services\AuditoriaService;

class SharedServiceProvider extends ServiceProvider
{
    /**
     * Register shared services (usados por múltiples dominios).
     */
    public function register(): void
    {
        // =====================================================
        // SERVICIOS COMPARTIDOS (Singleton)
        // =====================================================
        $this->app->singleton(AuditoriaService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
