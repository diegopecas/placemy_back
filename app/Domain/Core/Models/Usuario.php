<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    
    // Relaciones
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
        )->withPivot('fecha_asignacion');
    }
    
    // =====================================================
    // MÉTODOS HELPER PARA PERMISOS
    // =====================================================
    
    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermission(string $codigoPermiso): bool
    {
        foreach ($this->roles as $rol) {
            foreach ($rol->permisos as $permiso) {
                if ($permiso->codigo === $codigoPermiso) {
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * Verificar si el usuario tiene alguno de los permisos
     */
    public function hasAnyPermission(array $codigosPermisos): bool
    {
        foreach ($codigosPermisos as $codigo) {
            if ($this->hasPermission($codigo)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Verificar si el usuario tiene todos los permisos
     */
    public function hasAllPermissions(array $codigosPermisos): bool
    {
        foreach ($codigosPermisos as $codigo) {
            if (!$this->hasPermission($codigo)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole(string $nombreRol): bool
    {
        return $this->roles->contains('nombre', $nombreRol);
    }
    
    /**
     * Verificar si el usuario tiene alguno de los roles
     */
    public function hasAnyRole(array $nombresRoles): bool
    {
        foreach ($nombresRoles as $nombre) {
            if ($this->hasRole($nombre)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Verificar si el usuario es Super Administrador
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Administrador');
    }
    
    /**
     * Obtener todos los permisos del usuario (de todos sus roles)
     */
    public function getAllPermissions(): array
    {
        $permisos = [];
        foreach ($this->roles as $rol) {
            foreach ($rol->permisos as $permiso) {
                $permisos[$permiso->codigo] = $permiso;
            }
        }
        return array_values($permisos);
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