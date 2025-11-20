<?php

namespace App\Domain\Cliente\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cliente\Models\Resena;
use Illuminate\Database\Eloquent\Collection;

class ResenaRepository extends BaseRepository
{
    public function __construct(Resena $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar por cliente-establecimiento
     */
    public function findByClienteEstablecimiento(int $clienteEstablecimientoId): Collection
    {
        return $this->model::where('cliente_establecimiento_id', $clienteEstablecimientoId)
            ->orderBy('fecha_resena', 'desc')
            ->get();
    }
    
    /**
     * Buscar por establecimiento con filtros
     */
    public function findByEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        $query = $this->model::whereHas('clienteEstablecimiento', function($q) use ($establecimientoId) {
            $q->where('establecimiento_id', $establecimientoId);
        })->with(['clienteEstablecimiento.cliente.persona']);
        
        // Filtro por calificación
        if (isset($filtros['calificacion'])) {
            $query->where('calificacion', $filtros['calificacion']);
        }
        
        // Filtro por rango de calificación
        if (isset($filtros['calificacion_minima'])) {
            $query->where('calificacion', '>=', $filtros['calificacion_minima']);
        }
        
        // Filtro por pendientes de respuesta
        if (isset($filtros['sin_respuesta']) && $filtros['sin_respuesta']) {
            $query->whereNull('respuesta_establecimiento');
        }
        
        return $query->orderBy('fecha_resena', 'desc')->get();
    }
    
    /**
     * Buscar con relaciones
     */
    public function findByIdWithRelations(int $id): ?Resena
    {
        return $this->model::with([
            'clienteEstablecimiento.cliente.persona',
            'clienteEstablecimiento.establecimiento'
        ])->find($id);
    }
    
    /**
     * Calcular promedio de calificaciones de un establecimiento
     */
    public function calcularPromedioEstablecimiento(int $establecimientoId): float
    {
        return $this->model::whereHas('clienteEstablecimiento', function($q) use ($establecimientoId) {
            $q->where('establecimiento_id', $establecimientoId);
        })->avg('calificacion') ?? 0;
    }
}
