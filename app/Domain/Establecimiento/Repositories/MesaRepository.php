<?php

namespace App\Domain\Establecimiento\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Establecimiento\Models\Mesa;
use Illuminate\Database\Eloquent\Collection;

class MesaRepository extends BaseRepository
{
    public function __construct(Mesa $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar mesas por establecimiento con relaciones y filtros opcionales
     */
    public function findByEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        $query = $this->model::where('establecimiento_id', $establecimientoId)
            ->where('activo', true)
            ->with(['estado', 'zona', 'staffAsignado.persona']);
        
        // Aplicar filtros opcionales
        if (isset($filtros['zona_id'])) {
            $query->where('zona_id', $filtros['zona_id']);
        }
        
        if (isset($filtros['estado_id'])) {
            $query->where('estado_id', $filtros['estado_id']);
        }
        
        if (isset($filtros['capacidad_minima'])) {
            $query->where('capacidad', '>=', $filtros['capacidad_minima']);
        }
        
        return $query->orderBy('identificacion_mesa')->get();
    }
    
    /**
     * Buscar mesas por zona
     */
    public function findByZona(int $zonaId): Collection
    {
        return $this->model::where('zona_id', $zonaId)
            ->where('activo', true)
            ->with(['estado', 'zona', 'staffAsignado.persona'])
            ->orderBy('identificacion_mesa')
            ->get();
    }
    
    /**
     * Buscar mesas por estado
     */
    public function findByEstado(int $establecimientoId, int $estadoId): Collection
    {
        return $this->model::where('establecimiento_id', $establecimientoId)
            ->where('estado_id', $estadoId)
            ->where('activo', true)
            ->with(['estado', 'zona', 'staffAsignado.persona'])
            ->orderBy('identificacion_mesa')
            ->get();
    }
    
    /**
     * Verificar si existe identificación de mesa en establecimiento
     */
    public function existeIdentificacionMesa(int $establecimientoId, string $identificacionMesa, ?int $excludeId = null): bool
    {
        $query = $this->model::where('establecimiento_id', $establecimientoId)
            ->where('identificacion_mesa', $identificacionMesa);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar mesa con relaciones
     */
    public function findByIdWithRelations(int $id): ?Mesa
    {
        return $this->model::with([
            'establecimiento',
            'zona',
            'estado',
            'staffAsignado.persona'
        ])->find($id);
    }
    
    /**
     * Buscar mesas asignadas a staff
     */
    public function findByStaff(int $staffId): Collection
    {
        return $this->model::where('staff_asignado_id', $staffId)
            ->where('activo', true)
            ->with(['estado', 'zona', 'staffAsignado.persona'])
            ->orderBy('identificacion_mesa')
            ->get();
    }
}