<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'core_roles';

    public $timestamps = false;

    protected $fillable = [
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

    // Relaciones
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
        )->withPivot('fecha_asignacion');
    }

    // Métodos de negocio
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
}
