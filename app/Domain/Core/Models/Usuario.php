<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens;

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

    // Métodos de negocio
    public function estaActivo(): bool
    {
        return $this->activo == 1;
    }

    public function estaBloqueado(): bool
    {
        if (!$this->bloqueado_hasta) {
            return false;
        }

        return now()->lt($this->bloqueado_hasta);
    }

    public function tieneRol(string $nombreRol): bool
    {
        return $this->roles()->where('nombre', $nombreRol)->exists();
    }

    public function tienePermiso(string $codigoPermiso): bool
    {
        return $this->roles()
            ->whereHas('permisos', function ($query) use ($codigoPermiso) {
                $query->where('codigo', $codigoPermiso);
            })
            ->exists();
    }

    public function incrementarIntentosFallidos(): void
    {
        $this->increment('intentos_fallidos');

        // Bloquear después de 5 intentos fallidos
        if ($this->intentos_fallidos >= 5) {
            $this->update([
                'bloqueado_hasta' => now()->addMinutes(30)
            ]);
        }
    }

    public function resetearIntentosFallidos(): void
    {
        $this->update([
            'intentos_fallidos' => 0,
            'bloqueado_hasta' => null,
        ]);
    }
}
