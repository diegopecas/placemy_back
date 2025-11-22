<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\CuentaPago;
use Illuminate\Database\Eloquent\Collection;

class CuentaPagoRepository extends BaseRepository
{
    public function __construct(CuentaPago $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar pagos por cuenta
     */
    public function findByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->with(['division', 'metodoPago'])
            ->get();
    }
    
    /**
     * Buscar pagos por división
     */
    public function findByDivision(int $cuentaDivisionId): Collection
    {
        return $this->model::where('cuenta_division_id', $cuentaDivisionId)
            ->with('metodoPago')
            ->get();
    }
    
    /**
     * Calcular total pagado de una cuenta
     */
    public function calcularTotalPagado(int $cuentaId): float
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->sum('monto');
    }
    
    /**
     * Calcular total pagado de una división
     */
    public function calcularTotalPagadoDivision(int $cuentaDivisionId): float
    {
        return $this->model::where('cuenta_division_id', $cuentaDivisionId)
            ->sum('monto');
    }
    
    /**
     * Buscar pago con relaciones
     */
    public function findByIdWithRelations(int $id): ?CuentaPago
    {
        return $this->model::with([
            'cuenta',
            'division',
            'metodoPago'
        ])->find($id);
    }
}
