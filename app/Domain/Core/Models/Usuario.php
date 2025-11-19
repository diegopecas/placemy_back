<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Domain\Establecimiento\Models\Establecimiento;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $table = 'core_usuarios';
    
    protected $fillable = [
        'persona_id',
        'username',
        'email',
        'password',
        'email_verified_at',
        'activo',
        'ultimo_acceso',
        'intentos_fallidos',
        'bloqueado_hasta',
    ];
    
    protected $hidden = [
        'password',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'activo' => 'boolean',
        'ultimo_acceso' => 'datetime',
        'bloqueado_hasta' => 'datetime',
        'password' => 'hashed',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function persona()
    {
        return $this->belongsTo(PersonaNatural::class, 'persona_id');
    }
    
    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'core_usuarios_roles',
            'usuario_id',
            'rol_id'
        )->withPivot('fecha_asignacion', 'establecimiento_id');
    }
    
    /**
     * Obtener roles del usuario en un establecimiento específico
     */
    public function rolesEnEstablecimiento(int $establecimientoId)
    {
        return $this->belongsToMany(
            Rol::class,
            'core_usuarios_roles',
            'usuario_id',
            'rol_id'
        )->withPivot('fecha_asignacion', 'establecimiento_id')
         ->wherePivot('establecimiento_id', $establecimientoId);
    }
    
    /**
     * Obtener establecimientos donde el usuario tiene acceso
     */
    public function establecimientos()
    {
        return Establecimiento::whereIn('id', function ($query) {
            $query->select('establecimiento_id')
                  ->from('core_usuarios_roles')
                  ->where('usuario_id', $this->id);
        })->get();
    }
    
    // =====================================================
    // MÉTODOS HELPER PARA PERMISOS POR ESTABLECIMIENTO
    // =====================================================
    
    /**
     * Verificar si el usuario tiene un permiso en un establecimiento específico
     */
    public function hasPermissionInEstablecimiento(string $codigoPermiso, int $establecimientoId): bool
    {
        $roles = $this->rolesEnEstablecimiento($establecimientoId)->with('permisos')->get();
        
        foreach ($roles as $rol) {
            foreach ($rol->permisos as $permiso) {
                if ($permiso->codigo === $codigoPermiso) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Verificar si el usuario tiene alguno de los permisos en un establecimiento
     */
    public function hasAnyPermissionInEstablecimiento(array $codigosPermisos, int $establecimientoId): bool
    {
        foreach ($codigosPermisos as $codigo) {
            if ($this->hasPermissionInEstablecimiento($codigo, $establecimientoId)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Verificar si el usuario tiene todos los permisos en un establecimiento
     */
    public function hasAllPermissionsInEstablecimiento(array $codigosPermisos, int $establecimientoId): bool
    {
        foreach ($codigosPermisos as $codigo) {
            if (!$this->hasPermissionInEstablecimiento($codigo, $establecimientoId)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Verificar si el usuario tiene un rol en un establecimiento específico
     */
    public function hasRoleInEstablecimiento(string $nombreRol, int $establecimientoId): bool
    {
        return $this->rolesEnEstablecimiento($establecimientoId)
                    ->where('nombre', $nombreRol)
                    ->exists();
    }
    
    /**
     * Obtener todos los permisos del usuario en un establecimiento
     */
    public function getAllPermissionsInEstablecimiento(int $establecimientoId): array
    {
        $permisos = [];
        $roles = $this->rolesEnEstablecimiento($establecimientoId)->with('permisos')->get();
        
        foreach ($roles as $rol) {
            foreach ($rol->permisos as $permiso) {
                $permisos[$permiso->codigo] = $permiso;
            }
        }
        return array_values($permisos);
    }
    
    /**
     * Obtener IDs de establecimientos donde el usuario tiene acceso
     */
    public function getEstablecimientosIds(): array
    {
        return $this->roles()
                    ->select('core_usuarios_roles.establecimiento_id')
                    ->distinct()
                    ->pluck('establecimiento_id')
                    ->toArray();
    }
    
    // =====================================================
    // MÉTODOS DE NEGOCIO PARA AUTENTICACIÓN
    // =====================================================
    
    /**
     * Verificar si el usuario está activo
     */
    public function estaActivo(): bool
    {
        return $this->activo === true;
    }
    
    /**
     * Verificar si el usuario está bloqueado
     */
    public function estaBloqueado(): bool
    {
        if (!$this->bloqueado_hasta) {
            return false;
        }
        
        return now()->lessThan($this->bloqueado_hasta);
    }
    
    /**
     * Incrementar intentos fallidos de login
     */
    public function incrementarIntentosFallidos(): void
    {
        $this->intentos_fallidos++;
        
        // Si llega a 5 intentos, bloquear por 15 minutos
        if ($this->intentos_fallidos >= 5) {
            $this->bloqueado_hasta = now()->addMinutes(15);
        }
        
        $this->save();
    }
    
    /**
     * Resetear intentos fallidos
     */
    public function resetearIntentosFallidos(): void
    {
        $this->intentos_fallidos = 0;
        $this->bloqueado_hasta = null;
        $this->save();
    }
}
