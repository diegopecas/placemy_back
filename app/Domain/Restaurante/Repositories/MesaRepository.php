<?php

namespace App\Domain\Restaurante\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Restaurante\Models\Mesa;
use Illuminate\Database\Eloquent\Collection;

class MesaRepository extends BaseRepository
{
    public function __construct(Mesa $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar mesas por restaurante
     */
    public function findByRestaurante(int $restauranteId): Collection
    {
        return $this->model::where('restaurante_id', $restauranteId)
            ->where('activo', true)
            ->get();
    }
    
    /**
     * Buscar mesas por zona
     */
    public function findByZona(int $zonaId): Collection
    {
        return $this->model::where('zona_id', $zonaId)
            ->where('activo', true)
            ->get();
    }
    
    /**
     * Buscar mesas por estado
     */
    public function findByEstado(int $restauranteId, int $estadoId): Collection
    {
        return $this->model::where('restaurante_id', $restauranteId)
            ->where('estado_id', $estadoId)
            ->where('activo', true)
            ->get();
    }
    
    /**
     * Verificar si existe identificación de mesa en restaurante
     */
    public function existeIdentificacionMesa(int $restauranteId, string $identificacionMesa, ?int $excludeId = null): bool
    {
        $query = $this->model::where('restaurante_id', $restauranteId)
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
            'restaurante',
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
            ->get();
    }
}
