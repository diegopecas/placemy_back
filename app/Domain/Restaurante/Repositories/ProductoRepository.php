<?php

namespace App\Domain\Restaurante\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Restaurante\Models\Producto;
use Illuminate\Database\Eloquent\Collection;

class ProductoRepository extends BaseRepository
{
    public function __construct(Producto $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar producto por código
     */
    public function findByCodigo(string $codigoProducto): ?Producto
    {
        return $this->model::where('codigo_producto', $codigoProducto)->first();
    }
    
    /**
     * Verificar si existe código de producto
     */
    public function existeCodigo(string $codigoProducto, ?int $excludeId = null): bool
    {
        $query = $this->model::where('codigo_producto', $codigoProducto);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar producto con relaciones
     */
    public function findByIdWithRelations(int $id): ?Producto
    {
        return $this->model::with([
            'platos',
            'restaurantes'
        ])->find($id);
    }
    
    /**
     * Buscar productos de un restaurante
     */
    public function findByRestaurante(int $restauranteId): Collection
    {
        return $this->model::whereHas('restaurantes', function($query) use ($restauranteId) {
            $query->where('restaurantes.id', $restauranteId);
        })->get();
    }
    
    /**
     * Buscar productos disponibles de un restaurante
     */
    public function findDisponiblesByRestaurante(int $restauranteId): Collection
    {
        return $this->model::whereHas('restaurantes', function($query) use ($restauranteId) {
            $query->where('restaurantes.id', $restauranteId)
                  ->where('restaurante_productos.disponible', true)
                  ->where('restaurante_productos.activo', true);
        })->get();
    }
}
