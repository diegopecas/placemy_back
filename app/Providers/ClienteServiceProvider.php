<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Domain\Cliente\Contracts\ClienteServiceInterface;
use App\Domain\Cliente\Services\ClienteService;
use App\Domain\Cliente\Contracts\ClienteEstablecimientoServiceInterface;
use App\Domain\Cliente\Services\ClienteEstablecimientoService;
use App\Domain\Cliente\Contracts\CampaniaServiceInterface;
use App\Domain\Cliente\Services\CampaniaService;
use App\Domain\Cliente\Contracts\ResenaServiceInterface;
use App\Domain\Cliente\Services\ResenaService;

class ClienteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Cliente
        $this->app->bind(
            ClienteServiceInterface::class,
            ClienteService::class
        );
        
        // Cliente Establecimiento
        $this->app->bind(
            ClienteEstablecimientoServiceInterface::class,
            ClienteEstablecimientoService::class
        );
        
        // Campaña
        $this->app->bind(
            CampaniaServiceInterface::class,
            CampaniaService::class
        );
        
        // Reseña
        $this->app->bind(
            ResenaServiceInterface::class,
            ResenaService::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}