<?php

namespace App\Domain\Establecimiento\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Establecimiento\Models\Plato;
use Illuminate\Database\Eloquent\Collection;

class PlatoRepository extends BaseRepository
{
    public function __construct(Plato $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar platos por categoría
     */
    public function findByCategoria(int $categoriaId): Collection
    {
        return $this->model::where('categoria_id', $categoriaId)->get();
    }
    
    /**
     * Buscar plato por código
     */
    public function findByCodigo(string $codigoPlato): ?Plato
    {
        return $this->model::where('codigo_plato', $codigoPlato)->first();
    }
    
    /**
     * Verificar si existe código de plato
     */
    public function existeCodigo(string $codigoPlato, ?int $excludeId = null): bool
    {
        $query = $this->model::where('codigo_plato', $codigoPlato);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar plato con relaciones
     */
    public function findByIdWithRelations(int $id): ?Plato
    {
        return $this->model::with([
            'categoria.establecimiento',
            'alergenos',
            'productos',
            'establecimientos'
        ])->find($id);
    }
    
    /**
     * Buscar platos de un establecimiento
     */
    public function findByEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::whereHas('establecimientos', function($query) use ($establecimientoId) {
            $query->where('establecimientos.id', $establecimientoId);
        })->with(['alergenos', 'productos'])->get();
    }
    
    /**
     * Buscar platos disponibles de un establecimiento
     */
    public function findDisponiblesByEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::whereHas('establecimientos', function($query) use ($establecimientoId) {
            $query->where('establecimientos.id', $establecimientoId)
                  ->where('establecimiento_platos.disponible', true)
                  ->where('establecimiento_platos.activo', true);
        })->with(['alergenos', 'productos'])->get();
    }
}
