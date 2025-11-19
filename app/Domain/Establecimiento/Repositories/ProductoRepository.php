<?php

namespace App\Domain\Establecimiento\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Establecimiento\Models\Producto;
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
            'establecimientos'
        ])->find($id);
    }
    
    /**
     * Buscar productos de un establecimiento
     */
    public function findByEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::whereHas('establecimientos', function($query) use ($establecimientoId) {
            $query->where('establecimientos.id', $establecimientoId);
        })->get();
    }
    
    /**
     * Buscar productos disponibles de un establecimiento
     */
    public function findDisponiblesByEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::whereHas('establecimientos', function($query) use ($establecimientoId) {
            $query->where('establecimientos.id', $establecimientoId)
                  ->where('establecimiento_productos.disponible', true)
                  ->where('establecimiento_productos.activo', true);
        })->get();
    }
}
