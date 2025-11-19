<?php

namespace App\Domain\Establecimiento\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Establecimiento\Models\Establecimiento;
use Illuminate\Database\Eloquent\Collection;

class EstablecimientoRepository extends BaseRepository
{
    public function __construct(Establecimiento $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar establecimiento por slug
     */
    public function findBySlug(string $slug): ?Establecimiento
    {
        return $this->model::where('slug', $slug)->first();
    }
    
    /**
     * Verificar si existe slug
     */
    public function existeSlug(string $slug, ?int $excludeId = null): bool
    {
        $query = $this->model::where('slug', $slug);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar establecimientos activos y verificados
     */
    public function findActivosVerificados(): Collection
    {
        return $this->model::where('activo', true)
            ->where('verificado', true)
            ->get();
    }
    
    /**
     * Buscar establecimientos por ciudad
     */
    public function findByCiudad(int $ciudadId): Collection
    {
        return $this->model::where('ciudad_id', $ciudadId)
            ->where('activo', true)
            ->where('verificado', true)
            ->get();
    }
    
    /**
     * Buscar establecimientos por tipo de cocina
     */
    public function findByTipoCocina(int $tipoCocinaId): Collection
    {
        return $this->model::whereHas('tiposCocina', function($query) use ($tipoCocinaId) {
            $query->where('tipos_cocina.id', $tipoCocinaId);
        })->where('activo', true)
          ->where('verificado', true)
          ->get();
    }
    
    /**
     * Buscar establecimiento con relaciones completas
     */
    public function findByIdWithRelations(int $id): ?Establecimiento
    {
        return $this->model::with([
            'personaJuridica',
            'ciudad.departamento.pais',
            'tiposCocina',
            'rangoPrecio',
            'metodosPago',
            'caracteristicas',
            'gruposEmpresariales',
            'zonas',
            'categorias',
            'roles',
            'cargos',
            'configuraciones'
        ])->find($id);
    }
}
