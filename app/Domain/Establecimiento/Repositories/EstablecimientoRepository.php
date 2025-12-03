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
        return $this->model::whereHas('tiposCocina', function ($query) use ($tipoCocinaId) {
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
    /**
     * Obtener menú completo del establecimiento (platos y productos disponibles)
     */
    public function findMenuByEstablecimiento(int $establecimientoId): array
    {
        // Platos disponibles
        $platos = \App\Domain\Establecimiento\Models\EstablecimientoPlato::with([
            'plato.categoria',
            'plato.alergenos'
        ])
            ->where('establecimiento_id', $establecimientoId)
            ->where('disponible', true)
            ->where('activo', true)
            ->get()
            ->map(function ($ep) {
                return [
                    'id' => $ep->plato->id,
                    'nombre' => $ep->plato->nombre,
                    'descripcion' => $ep->plato->descripcion,
                    'precio' => (float) $ep->precio,
                    'disponible' => $ep->disponible,
                    'activo' => $ep->activo,
                    'foto_url' => $ep->plato->foto_url,
                    'video_url' => $ep->plato->video_url,
                    'tiempo_preparacion_min' => $ep->plato->tiempo_preparacion_min,
                    'calificacion_promedio' => $ep->calificacion_promedio,
                    'categoria' => $ep->plato->categoria ? [
                        'id' => $ep->plato->categoria->id,
                        'nombre' => $ep->plato->categoria->nombre
                    ] : null,
                    'alergenos' => $ep->plato->alergenos->map(function ($a) {
                        return [
                            'id' => $a->id,
                            'nombre' => $a->nombre,
                            'icono' => $a->icono
                        ];
                    })->toArray()
                ];
            })
            ->toArray();

        // Productos disponibles
        $productos = \App\Domain\Establecimiento\Models\EstablecimientoProducto::with(['producto'])
            ->where('establecimiento_id', $establecimientoId)
            ->where('disponible', true)
            ->where('activo', true)
            ->get()
            ->map(function ($ep) {
                return [
                    'id' => $ep->producto->id,
                    'nombre' => $ep->producto->nombre,
                    'descripcion' => $ep->producto->descripcion,
                    'precio_individual' => (float) $ep->precio_individual,
                    'disponible' => $ep->disponible,
                    'activo' => $ep->activo,
                    'foto_url' => $ep->producto->foto_url
                ];
            })
            ->toArray();

        return [
            'platos' => $platos,
            'productos' => $productos
        ];
    }
}
