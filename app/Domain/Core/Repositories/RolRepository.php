<?php

namespace App\Domain\Core\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Core\Models\Rol;
use Illuminate\Database\Eloquent\Collection;

class RolRepository extends BaseRepository
{
    public function __construct(Rol $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar rol por nombre
     */
    public function findByNombre(string $nombre): ?Rol
    {
        return $this->model::where('nombre', $nombre)->first();
    }
    
    /**
     * Buscar roles activos
     */
    public function findActivos(): Collection
    {
        return $this->model::where('activo', true)->get();
    }
    
    /**
     * Buscar rol con permisos
     */
    public function findByIdWithPermisos(int $id): ?Rol
    {
        return $this->model::with('permisos')->find($id);
    }
    
    /**
     * Verificar si rol tiene permiso específico
     */
    public function tienePermiso(int $rolId, string $codigoPermiso): bool
    {
        $rol = $this->findByIdOrFail($rolId);
        return $rol->tienePermiso($codigoPermiso);
    }
    
    /**
     * Asignar permiso a rol
     */
    public function asignarPermiso(int $rolId, int $permisoId): void
    {
        $rol = $this->findByIdOrFail($rolId);
        $rol->asignarPermiso($permisoId);
    }
    
    /**
     * Remover permiso de rol
     */
    public function removerPermiso(int $rolId, int $permisoId): void
    {
        $rol = $this->findByIdOrFail($rolId);
        $rol->removerPermiso($permisoId);
    }
    
    /**
     * Obtener usuarios con este rol
     */
    public function getUsuarios(int $rolId): Collection
    {
        $rol = $this->findByIdOrFail($rolId);
        return $rol->usuarios;
    }
}
