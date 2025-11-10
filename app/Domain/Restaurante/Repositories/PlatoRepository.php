<?php

namespace App\Domain\Restaurante\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Restaurante\Models\Plato;
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
            'categoria.restaurante',
            'alergenos',
            'productos',
            'restaurantes'
        ])->find($id);
    }
    
    /**
     * Buscar platos de un restaurante
     */
    public function findByRestaurante(int $restauranteId): Collection
    {
        return $this->model::whereHas('restaurantes', function($query) use ($restauranteId) {
            $query->where('restaurantes.id', $restauranteId);
        })->with(['alergenos', 'productos'])->get();
    }
    
    /**
     * Buscar platos disponibles de un restaurante
     */
    public function findDisponiblesByRestaurante(int $restauranteId): Collection
    {
        return $this->model::whereHas('restaurantes', function($query) use ($restauranteId) {
            $query->where('restaurantes.id', $restauranteId)
                  ->where('restaurante_platos.disponible', true)
                  ->where('restaurante_platos.activo', true);
        })->with(['alergenos', 'productos'])->get();
    }
}
