<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\CuentaDivision;
use Illuminate\Database\Eloquent\Collection;

class CuentaDivisionRepository extends BaseRepository
{
    public function __construct(CuentaDivision $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar divisiones por cuenta
     */
    public function findByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->with(['itemsAsignados.item', 'pagos'])
            ->get();
    }
    
    /**
     * Buscar divisiones pendientes de pago
     */
    public function findPendientesPagoByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->where('pagado', false)
            ->with(['itemsAsignados.item'])
            ->get();
    }
    
    /**
     * Buscar división con relaciones
     */
    public function findByIdWithRelations(int $id): ?CuentaDivision
    {
        return $this->model::with([
            'cuenta',
            'itemsAsignados.item.tipoItem',
            'itemsAsignados.item.plato.plato',
            'itemsAsignados.item.producto.producto',
            'pagos.metodoPago'
        ])->find($id);
    }
    
    /**
     * Verificar si todas las divisiones están pagadas
     */
    public function todasPagadas(int $cuentaId): bool
    {
        return !$this->model::where('cuenta_id', $cuentaId)
            ->where('pagado', false)
            ->exists();
    }
}
