<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\CuentaImpuesto;
use Illuminate\Database\Eloquent\Collection;

class CuentaImpuestoRepository extends BaseRepository
{
    public function __construct(CuentaImpuesto $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar impuestos por cuenta
     */
    public function findByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->with('tipoImpuesto')
            ->get();
    }
    
    /**
     * Calcular total de impuestos de una cuenta
     */
    public function calcularTotalImpuestosCuenta(int $cuentaId): float
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->sum('monto');
    }
    
    /**
     * Eliminar impuestos de una cuenta
     */
    public function deleteByClauenta(int $cuentaId): bool
    {
        return $this->model::where('cuenta_id', $cuentaId)->delete();
    }
}
