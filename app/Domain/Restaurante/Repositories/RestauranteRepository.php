<?php

namespace App\Domain\Restaurante\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Restaurante\Models\Restaurante;
use Illuminate\Database\Eloquent\Collection;

class RestauranteRepository extends BaseRepository
{
    public function __construct(Restaurante $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar restaurante por slug
     */
    public function findBySlug(string $slug): ?Restaurante
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
     * Buscar restaurantes activos y verificados
     */
    public function findActivosVerificados(): Collection
    {
        return $this->model::where('activo', true)
            ->where('verificado', true)
            ->get();
    }
    
    /**
     * Buscar restaurantes por ciudad
     */
    public function findByCiudad(int $ciudadId): Collection
    {
        return $this->model::where('ciudad_id', $ciudadId)
            ->where('activo', true)
            ->where('verificado', true)
            ->get();
    }
    
    /**
     * Buscar restaurantes por tipo de cocina
     */
    public function findByTipoCocina(int $tipoCocinaId): Collection
    {
        return $this->model::where('tipo_cocina_id', $tipoCocinaId)
            ->where('activo', true)
            ->where('verificado', true)
            ->get();
    }
    
    /**
     * Buscar restaurante con relaciones completas
     */
    public function findByIdWithRelations(int $id): ?Restaurante
    {
        return $this->model::with([
            'personaJuridica',
            'ciudad.departamento.pais',
            'tipoCocina',
            'rangoPrecio',
            'metodosPago',
            'caracteristicas',
            'gruposEmpresariales',
            'zonas',
            'categorias'
        ])->find($id);
    }
}
