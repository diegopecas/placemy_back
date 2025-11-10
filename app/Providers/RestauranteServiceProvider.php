<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Domain\Restaurante\Repositories\RestauranteRepository;
use App\Domain\Restaurante\Repositories\MesaRepository;
use App\Domain\Restaurante\Repositories\PlatoRepository;
use App\Domain\Restaurante\Repositories\ProductoRepository;
use App\Domain\Restaurante\Repositories\StaffRepository;

// Contracts (Interfaces)
use App\Domain\Restaurante\Contracts\RestauranteServiceInterface;
use App\Domain\Restaurante\Contracts\MesaServiceInterface;
use App\Domain\Restaurante\Contracts\PlatoServiceInterface;
use App\Domain\Restaurante\Contracts\ProductoServiceInterface;
use App\Domain\Restaurante\Contracts\StaffServiceInterface;

// Services (Implementaciones)
use App\Domain\Restaurante\Services\RestauranteService;
use App\Domain\Restaurante\Services\MesaService;
use App\Domain\Restaurante\Services\PlatoService;
use App\Domain\Restaurante\Services\ProductoService;
use App\Domain\Restaurante\Services\StaffService;

class RestauranteServiceProvider extends ServiceProvider
{
    /**
     * Register Restaurante domain services.
     */
    public function register(): void
    {
        // =====================================================
        // REGISTRAR REPOSITORIES (Singleton)
        // =====================================================
        $this->app->singleton(RestauranteRepository::class);
        $this->app->singleton(MesaRepository::class);
        $this->app->singleton(PlatoRepository::class);
        $this->app->singleton(ProductoRepository::class);
        $this->app->singleton(StaffRepository::class);
        
        // =====================================================
        // REGISTRAR SERVICES CON INTERFACES
        // =====================================================
        $this->app->bind(RestauranteServiceInterface::class, RestauranteService::class);
        $this->app->bind(MesaServiceInterface::class, MesaService::class);
        $this->app->bind(PlatoServiceInterface::class, PlatoService::class);
        $this->app->bind(ProductoServiceInterface::class, ProductoService::class);
        $this->app->bind(StaffServiceInterface::class, StaffService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
