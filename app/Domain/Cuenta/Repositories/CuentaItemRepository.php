<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\CuentaItem;
use Illuminate\Database\Eloquent\Collection;

class CuentaItemRepository extends BaseRepository
{
    public function __construct(CuentaItem $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar items por cuenta
     */
    public function findByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->with(['tipoItem', 'plato.plato', 'producto.producto', 'estado'])
            ->get();
    }
    
    /**
     * Buscar items por estado
     */
    public function findByCuentaYEstado(int $cuentaId, int $estadoId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->where('estado_id', $estadoId)
            ->with(['tipoItem', 'plato.plato', 'producto.producto', 'estado'])
            ->get();
    }
    
    /**
     * Buscar items modificables de una cuenta
     */
    public function findModificablesByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->whereHas('estado', function($query) {
                $query->where('permite_modificacion', true);
            })
            ->with(['tipoItem', 'plato.plato', 'producto.producto', 'estado'])
            ->get();
    }
    
    /**
     * Buscar item con relaciones
     */
    public function findByIdWithRelations(int $id): ?CuentaItem
    {
        return $this->model::with([
            'cuenta',
            'tipoItem',
            'plato.plato',
            'producto.producto',
            'estado',
            'divisiones.division'
        ])->find($id);
    }
    
    /**
     * Calcular subtotal de items por cuenta
     */
    public function calcularSubtotalCuenta(int $cuentaId): float
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->sum('subtotal');
    }
}
