<?php

namespace App\Domain\Establecimiento\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Establecimiento\Models\Staff;
use Illuminate\Database\Eloquent\Collection;

class StaffRepository extends BaseRepository
{
    public function __construct(Staff $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar staff por código de empleado
     */
    public function findByCodigo(string $codigoEmpleado): ?Staff
    {
        return $this->model::where('codigo_empleado', $codigoEmpleado)->first();
    }
    
    /**
     * Buscar staff por persona
     */
    public function findByPersona(int $personaId): ?Staff
    {
        return $this->model::where('persona_id', $personaId)->first();
    }
    
    /**
     * Verificar si existe código de empleado
     */
    public function existeCodigo(string $codigoEmpleado, ?int $excludeId = null): bool
    {
        $query = $this->model::where('codigo_empleado', $codigoEmpleado);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar staff activo
     */
    public function findActivos(): Collection
    {
        return $this->model::where('activo', true)->get();
    }
    
    /**
     * Buscar staff con relaciones
     */
    public function findByIdWithRelations(int $id): ?Staff
    {
        return $this->model::with([
            'persona.tipoDocumento',
            'persona.ciudadResidencia',
            'establecimientos',
            'mesasAsignadas'
        ])->find($id);
    }
    
    /**
     * Buscar staff por establecimiento
     */
    public function findByEstablecimiento(int $establecimientoId): Collection
    {
        return $this->model::whereHas('establecimientos', function($query) use ($establecimientoId) {
            $query->where('establecimientos.id', $establecimientoId)
                  ->where('establecimiento_staff.activo', true);
        })->with('persona')->get();
    }
    
    /**
     * Buscar staff por establecimiento y cargo
     */
    public function findByEstablecimientoYCargo(int $establecimientoId, int $cargoId): Collection
    {
        return $this->model::whereHas('establecimientos', function($query) use ($establecimientoId, $cargoId) {
            $query->where('establecimientos.id', $establecimientoId)
                  ->where('establecimiento_staff.cargo_id', $cargoId)
                  ->where('establecimiento_staff.activo', true);
        })->with('persona')->get();
    }
}
