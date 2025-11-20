<?php

namespace App\Domain\Establecimiento\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Establecimiento\Models\EstablecimientoStaff;
use Illuminate\Database\Eloquent\Collection;

class EstablecimientoStaffRepository extends BaseRepository
{
    public function __construct(EstablecimientoStaff $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar por código de empleado en un establecimiento
     */
    public function findByCodigo(string $codigoEmpleado, int $establecimientoId): ?EstablecimientoStaff
    {
        return $this->model::where('codigo_empleado', $codigoEmpleado)
            ->where('establecimiento_id', $establecimientoId)
            ->first();
    }
    
    /**
     * Buscar por usuario en un establecimiento
     */
    public function findByUsuario(int $usuarioId, int $establecimientoId): ?EstablecimientoStaff
    {
        return $this->model::where('usuario_id', $usuarioId)
            ->where('establecimiento_id', $establecimientoId)
            ->first();
    }
    
    /**
     * Verificar si existe código de empleado en un establecimiento
     */
    public function existeCodigo(string $codigoEmpleado, int $establecimientoId, ?int $excludeId = null): bool
    {
        $query = $this->model::where('codigo_empleado', $codigoEmpleado)
            ->where('establecimiento_id', $establecimientoId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar staff activo por establecimiento
     */
    public function findActivosPorEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::where('establecimiento_id', $establecimientoId)
            ->where('activo', true)
            ->with(['usuario.persona', 'cargo'])
            ->get();
    }
    
    /**
     * Buscar por ID con relaciones
     */
    public function findByIdWithRelations(int $id): ?EstablecimientoStaff
    {
        return $this->model::with([
            'establecimiento',
            'cargo',
            'usuario.persona',
            'mesasAsignadas'
        ])->find($id);
    }
    
    /**
     * Buscar por establecimiento con filtros
     */
    public function findByEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        $query = $this->model::where('establecimiento_id', $establecimientoId)
            ->with(['usuario.persona', 'cargo']);
        
        // Filtro por cargo
        if (isset($filtros['cargo_id'])) {
            $query->where('cargo_id', $filtros['cargo_id']);
        }
        
        // Filtro por estado activo
        if (isset($filtros['activo'])) {
            $query->where('activo', $filtros['activo']);
        }
        
        // Búsqueda por nombre o código
        if (isset($filtros['busqueda'])) {
            $busqueda = $filtros['busqueda'];
            $query->where(function($q) use ($busqueda) {
                $q->where('codigo_empleado', 'like', "%{$busqueda}%")
                  ->orWhereHas('usuario.persona', function($subq) use ($busqueda) {
                      $subq->where('nombres', 'like', "%{$busqueda}%")
                           ->orWhere('apellidos', 'like', "%{$busqueda}%");
                  });
            });
        }
        
        return $query->get();
    }
    
    /**
     * Buscar por establecimiento y cargo
     */
    public function findByEstablecimientoYCargo(int $establecimientoId, int $cargoId): Collection
    {
        return $this->model::where('establecimiento_id', $establecimientoId)
            ->where('cargo_id', $cargoId)
            ->where('activo', true)
            ->with(['usuario.persona'])
            ->get();
    }
    
    /**
     * Verificar si usuario ya está asignado en establecimiento
     */
    public function existeUsuarioEnEstablecimiento(int $usuarioId, int $establecimientoId, ?int $excludeId = null): bool
    {
        $query = $this->model::where('usuario_id', $usuarioId)
            ->where('establecimiento_id', $establecimientoId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
}