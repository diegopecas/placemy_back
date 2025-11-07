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
use App\Domain\Auth\Services\AuthService;
use App\Domain\Shared\Services\AuditoriaService;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Registrar Repositories como Singleton
        $this->app->singleton(PersonaNaturalRepository::class);
        $this->app->singleton(UsuarioRepository::class);
        $this->app->singleton(RolRepository::class);
        
        // Registrar Services compartidos como Singleton
        $this->app->singleton(AuditoriaService::class);
        
        // Registrar Services de dominio
        $this->app->bind(PersonaNaturalService::class);
        $this->app->bind(UsuarioService::class);
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
