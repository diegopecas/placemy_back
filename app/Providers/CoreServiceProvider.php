<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Domain\Core\Repositories\PersonaNaturalRepository;
use App\Domain\Core\Repositories\UsuarioRepository;
use App\Domain\Core\Repositories\RolRepository;

// Services
use App\Domain\Core\Services\PersonaNaturalService;
use App\Domain\Core\Services\UsuarioService;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register Core domain services.
     */
    public function register(): void
    {
        // =====================================================
        // REGISTRAR REPOSITORIES (Singleton)
        // =====================================================
        $this->app->singleton(PersonaNaturalRepository::class);
        $this->app->singleton(UsuarioRepository::class);
        $this->app->singleton(RolRepository::class);
        
        // =====================================================
        // REGISTRAR SERVICES
        // =====================================================
        $this->app->bind(PersonaNaturalService::class);
        $this->app->bind(UsuarioService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
