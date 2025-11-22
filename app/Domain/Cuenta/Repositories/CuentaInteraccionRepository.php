<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\CuentaInteraccion;
use Illuminate\Database\Eloquent\Collection;

class CuentaInteraccionRepository extends BaseRepository
{
    public function __construct(CuentaInteraccion $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar interacciones por cuenta
     */
    public function findByCuenta(int $cuentaId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->with(['tipoInteraccion.categoria', 'estado', 'atendidoPor.usuario.persona'])
            ->orderBy('fecha_interaccion', 'desc')
            ->get();
    }
    
    /**
     * Buscar interacciones por estado
     */
    public function findByCuentaYEstado(int $cuentaId, int $estadoId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->where('estado_id', $estadoId)
            ->with(['tipoInteraccion.categoria', 'estado'])
            ->orderBy('fecha_interaccion', 'desc')
            ->get();
    }
    
    /**
     * Buscar interacciones pendientes por establecimiento
     */
    public function findPendientesByEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::whereHas('cuenta', function($query) use ($establecimientoId) {
                $query->where('establecimiento_id', $establecimientoId);
            })
            ->whereHas('estado', function($query) {
                $query->where('codigo', 'PENDIENTE');
            })
            ->with(['cuenta.mesa', 'tipoInteraccion.categoria', 'estado'])
            ->orderBy('fecha_interaccion', 'asc')
            ->get();
    }
    
    /**
     * Buscar interacciones por tipo
     */
    public function findByCuentaYTipo(int $cuentaId, int $tipoInteraccionId): Collection
    {
        return $this->model::where('cuenta_id', $cuentaId)
            ->where('tipo_interaccion_id', $tipoInteraccionId)
            ->with(['tipoInteraccion.categoria', 'estado', 'atendidoPor.usuario.persona'])
            ->orderBy('fecha_interaccion', 'desc')
            ->get();
    }
    
    /**
     * Buscar interacción con relaciones
     */
    public function findByIdWithRelations(int $id): ?CuentaInteraccion
    {
        return $this->model::with([
            'cuenta.mesa',
            'cuenta.establecimiento',
            'tipoInteraccion.categoria',
            'estado',
            'atendidoPor.usuario.persona'
        ])->find($id);
    }
}
