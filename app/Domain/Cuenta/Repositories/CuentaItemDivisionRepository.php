<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\CuentaItemDivision;
use Illuminate\Database\Eloquent\Collection;

class CuentaItemDivisionRepository extends BaseRepository
{
    public function __construct(CuentaItemDivision $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar asignaciones por item
     */
    public function findByItem(int $cuentaItemId): Collection
    {
        return $this->model::where('cuenta_item_id', $cuentaItemId)
            ->with('division')
            ->get();
    }
    
    /**
     * Buscar asignaciones por división
     */
    public function findByDivision(int $cuentaDivisionId): Collection
    {
        return $this->model::where('cuenta_division_id', $cuentaDivisionId)
            ->with(['item.tipoItem', 'item.plato.plato', 'item.producto.producto'])
            ->get();
    }
    
    /**
     * Eliminar asignaciones de un item
     */
    public function deleteByItem(int $cuentaItemId): bool
    {
        return $this->model::where('cuenta_item_id', $cuentaItemId)->delete();
    }
    
    /**
     * Eliminar asignaciones de una división
     */
    public function deleteByDivision(int $cuentaDivisionId): bool
    {
        return $this->model::where('cuenta_division_id', $cuentaDivisionId)->delete();
    }
}
