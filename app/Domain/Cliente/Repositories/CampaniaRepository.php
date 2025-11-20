<?php

namespace App\Domain\Cliente\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cliente\Models\Campania;
use Illuminate\Database\Eloquent\Collection;

class CampaniaRepository extends BaseRepository
{
    public function __construct(Campania $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar por establecimiento
     */
    public function findByEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        $query = $this->model::where('establecimiento_id', $establecimientoId)
            ->with(['tipoCampania']);
        
        // Filtro por activo
        if (isset($filtros['activo'])) {
            $query->where('activo', $filtros['activo']);
        }
        
        // Filtro por tipo de campaña
        if (isset($filtros['tipo_campania_id'])) {
            $query->where('tipo_campania_id', $filtros['tipo_campania_id']);
        }
        
        // Filtro por vigencia
        if (isset($filtros['vigente'])) {
            $hoy = now()->format('Y-m-d');
            $query->where('fecha_inicio', '<=', $hoy)
                  ->where('fecha_fin', '>=', $hoy);
        }
        
        return $query->orderBy('fecha_inicio', 'desc')->get();
    }
    
    /**
     * Buscar con relaciones
     */
    public function findByIdWithRelations(int $id): ?Campania
    {
        return $this->model::with([
            'establecimiento',
            'tipoCampania',
            'clientesCampanias'
        ])->find($id);
    }
    
    /**
     * Buscar campañas activas y vigentes de un establecimiento
     */
    public function findActivasVigentes(int $establecimientoId): Collection
    {
        $hoy = now()->format('Y-m-d');
        
        return $this->model::where('establecimiento_id', $establecimientoId)
            ->where('activo', true)
            ->where('fecha_inicio', '<=', $hoy)
            ->where('fecha_fin', '>=', $hoy)
            ->get();
    }
}
