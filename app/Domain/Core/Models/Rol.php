<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\Establecimiento;

class Rol extends Model
{
    protected $table = 'core_roles';

    public $timestamps = false;

    protected $fillable = [
        'establecimiento_id',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_creacion' => 'datetime',
    ];

    const CREATED_AT = 'fecha_creacion';
    const UPDATED_AT = null;

    // =====================================================
    // RELACIONES
    // =====================================================

    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }

    public function permisos()
    {
        return $this->belongsToMany(
            Permiso::class,
            'core_roles_permisos',
            'rol_id',
            'permiso_id'
        );
    }

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'core_usuarios_roles',
            'rol_id',
            'usuario_id'
        )->withPivot('fecha_asignacion', 'establecimiento_id');
    }

    // =====================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================

    public function tienePermiso(string $codigoPermiso): bool
    {
        return $this->permisos()->where('codigo', $codigoPermiso)->exists();
    }

    public function asignarPermiso(int $permisoId): void
    {
        if (!$this->permisos()->where('permiso_id', $permisoId)->exists()) {
            $this->permisos()->attach($permisoId);
        }
    }

    public function removerPermiso(int $permisoId): void
    {
        $this->permisos()->detach($permisoId);
    }
    
    /**
     * Asignar múltiples permisos al rol
     */
    public function sincronizarPermisos(array $permisosIds): void
    {
        $this->permisos()->sync($permisosIds);
    }
    
    /**
     * Obtener todos los códigos de permisos
     */
    public function getCodigosPermisos(): array
    {
        return $this->permisos()->pluck('codigo')->toArray();
    }
}
