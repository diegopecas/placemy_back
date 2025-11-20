<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Contracts
use App\Domain\Establecimiento\Contracts\EstablecimientoServiceInterface;
use App\Domain\Establecimiento\Contracts\MesaServiceInterface;
use App\Domain\Establecimiento\Contracts\PlatoServiceInterface;
use App\Domain\Establecimiento\Contracts\ProductoServiceInterface;
use App\Domain\Establecimiento\Contracts\EstablecimientoStaffServiceInterface;

// Services
use App\Domain\Establecimiento\Services\EstablecimientoService;
use App\Domain\Establecimiento\Services\MesaService;
use App\Domain\Establecimiento\Services\PlatoService;
use App\Domain\Establecimiento\Services\ProductoService;
use App\Domain\Establecimiento\Services\EstablecimientoStaffService;

class EstablecimientoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Contracts to Implementations
        $this->app->bind(EstablecimientoServiceInterface::class, EstablecimientoService::class);
        $this->app->bind(MesaServiceInterface::class, MesaService::class);
        $this->app->bind(PlatoServiceInterface::class, PlatoService::class);
        $this->app->bind(ProductoServiceInterface::class, ProductoService::class);
        $this->app->bind(EstablecimientoStaffServiceInterface::class, EstablecimientoStaffService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}