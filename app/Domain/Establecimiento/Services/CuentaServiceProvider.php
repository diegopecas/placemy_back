<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Contracts
use App\Domain\Cuenta\Contracts\CuentaServiceInterface;
use App\Domain\Cuenta\Contracts\CuentaItemServiceInterface;
use App\Domain\Cuenta\Contracts\CuentaDivisionServiceInterface;
use App\Domain\Cuenta\Contracts\CuentaPagoServiceInterface;
use App\Domain\Cuenta\Contracts\CuentaInteraccionServiceInterface;

// Services
use App\Domain\Cuenta\Services\CuentaService;
use App\Domain\Cuenta\Services\CuentaItemService;
use App\Domain\Cuenta\Services\CuentaDivisionService;
use App\Domain\Cuenta\Services\CuentaPagoService;
use App\Domain\Cuenta\Services\CuentaInteraccionService;

// Repositories
use App\Domain\Cuenta\Repositories\CuentaRepository;
use App\Domain\Cuenta\Repositories\CuentaItemRepository;
use App\Domain\Cuenta\Repositories\CuentaImpuestoRepository;
use App\Domain\Cuenta\Repositories\CuentaDivisionRepository;
use App\Domain\Cuenta\Repositories\CuentaItemDivisionRepository;
use App\Domain\Cuenta\Repositories\CuentaPagoRepository;
use App\Domain\Cuenta\Repositories\CuentaInteraccionRepository;

// Models
use App\Domain\Cuenta\Models\Cuenta;
use App\Domain\Cuenta\Models\CuentaItem;
use App\Domain\Cuenta\Models\CuentaImpuesto;
use App\Domain\Cuenta\Models\CuentaDivision;
use App\Domain\Cuenta\Models\CuentaItemDivision;
use App\Domain\Cuenta\Models\CuentaPago;
use App\Domain\Cuenta\Models\CuentaInteraccion;

class CuentaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // =====================================================
        // REGISTRAR REPOSITORIES
        // =====================================================
        
        $this->app->singleton(CuentaRepository::class, function ($app) {
            return new CuentaRepository(new Cuenta());
        });
        
        $this->app->singleton(CuentaItemRepository::class, function ($app) {
            return new CuentaItemRepository(new CuentaItem());
        });
        
        $this->app->singleton(CuentaImpuestoRepository::class, function ($app) {
            return new CuentaImpuestoRepository(new CuentaImpuesto());
        });
        
        $this->app->singleton(CuentaDivisionRepository::class, function ($app) {
            return new CuentaDivisionRepository(new CuentaDivision());
        });
        
        $this->app->singleton(CuentaItemDivisionRepository::class, function ($app) {
            return new CuentaItemDivisionRepository(new CuentaItemDivision());
        });
        
        $this->app->singleton(CuentaPagoRepository::class, function ($app) {
            return new CuentaPagoRepository(new CuentaPago());
        });
        
        $this->app->singleton(CuentaInteraccionRepository::class, function ($app) {
            return new CuentaInteraccionRepository(new CuentaInteraccion());
        });
        
        // =====================================================
        // REGISTRAR SERVICES (BINDINGS DE CONTRATOS)
        // =====================================================
        
        $this->app->bind(CuentaServiceInterface::class, CuentaService::class);
        $this->app->bind(CuentaItemServiceInterface::class, CuentaItemService::class);
        $this->app->bind(CuentaDivisionServiceInterface::class, CuentaDivisionService::class);
        $this->app->bind(CuentaPagoServiceInterface::class, CuentaPagoService::class);
        $this->app->bind(CuentaInteraccionServiceInterface::class, CuentaInteraccionService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
