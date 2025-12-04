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
        // ✅ BINDINGS DE SERVICIOS DE DOMINIO
        
        // Cuenta
        $this->app->bind(
            \App\Domain\Cuenta\Contracts\CuentaServiceInterface::class,
            \App\Domain\Cuenta\Services\CuentaService::class
        );
        
        // Establecimiento
        $this->app->bind(
            \App\Domain\Establecimiento\Contracts\EstablecimientoServiceInterface::class,
            \App\Domain\Establecimiento\Services\EstablecimientoService::class
        );
        
        // Mesa (si existe)
        if (interface_exists(\App\Domain\Mesa\Contracts\MesaServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Mesa\Contracts\MesaServiceInterface::class,
                \App\Domain\Mesa\Services\MesaService::class
            );
        }
        
        // Cliente (si existe)
        if (interface_exists(\App\Domain\Cliente\Contracts\ClienteServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Cliente\Contracts\ClienteServiceInterface::class,
                \App\Domain\Cliente\Services\ClienteService::class
            );
        }
        
        // Plato (si existe)
        if (interface_exists(\App\Domain\Plato\Contracts\PlatoServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Plato\Contracts\PlatoServiceInterface::class,
                \App\Domain\Plato\Services\PlatoService::class
            );
        }
        
        // Producto (si existe)
        if (interface_exists(\App\Domain\Producto\Contracts\ProductoServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Producto\Contracts\ProductoServiceInterface::class,
                \App\Domain\Producto\Services\ProductoService::class
            );
        }
        
        // Usuario (si existe)
        if (interface_exists(\App\Domain\Usuario\Contracts\UsuarioServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Usuario\Contracts\UsuarioServiceInterface::class,
                \App\Domain\Usuario\Services\UsuarioService::class
            );
        }
        
        // Zona (si existe)
        if (interface_exists(\App\Domain\Zona\Contracts\ZonaServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Zona\Contracts\ZonaServiceInterface::class,
                \App\Domain\Zona\Services\ZonaService::class
            );
        }
        
        // Categoría (si existe)
        if (interface_exists(\App\Domain\Categoria\Contracts\CategoriaServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Categoria\Contracts\CategoriaServiceInterface::class,
                \App\Domain\Categoria\Services\CategoriaService::class
            );
        }
        
        // Staff (si existe)
        if (interface_exists(\App\Domain\Staff\Contracts\StaffServiceInterface::class)) {
            $this->app->bind(
                \App\Domain\Staff\Contracts\StaffServiceInterface::class,
                \App\Domain\Staff\Services\StaffService::class
            );
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}