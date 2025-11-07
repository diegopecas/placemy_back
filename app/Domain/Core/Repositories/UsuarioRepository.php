<?php

namespace App\Domain\Core\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Core\Models\Usuario;
use Illuminate\Database\Eloquent\Collection;

class UsuarioRepository extends BaseRepository
{
    public function __construct(Usuario $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar usuario por username
     */
    public function findByUsername(string $username): ?Usuario
    {
        return $this->model::where('username', $username)->first();
    }
    
    /**
     * Buscar usuario por email
     */
    public function findByEmail(string $email): ?Usuario
    {
        return $this->model::where('email', $email)->first();
    }
    
    /**
     * Buscar usuario por username o email
     */
    public function findByUsernameOrEmail(string $identifier): ?Usuario
    {
        return $this->model::where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();
    }
    
    /**
     * Buscar usuario con persona y roles
     */
    public function findByIdWithRelations(int $id): ?Usuario
    {
        return $this->model::with(['persona', 'roles.permisos'])->find($id);
    }
    
    /**
     * Buscar usuario por persona_id
     */
    public function findByPersonaId(int $personaId): ?Usuario
    {
        return $this->model::where('persona_id', $personaId)->first();
    }
    
    /**
     * Verificar si username existe
     */
    public function existeUsername(string $username, ?int $excludeId = null): bool
    {
        $query = $this->model::where('username', $username);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Verificar si email existe
     */
    public function existeEmail(string $email, ?int $excludeId = null): bool
    {
        $query = $this->model::where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar usuarios activos
     */
    public function findActivos(): Collection
    {
        return $this->model::where('activo', true)->get();
    }
    
    /**
     * Buscar usuarios por rol
     */
    public function findByRol(string $nombreRol): Collection
    {
        return $this->model::whereHas('roles', function ($query) use ($nombreRol) {
            $query->where('nombre', $nombreRol);
        })->get();
    }
    
    /**
     * Actualizar último acceso
     */
    public function actualizarUltimoAcceso(int $id): void
    {
        $this->model::where('id', $id)->update([
            'ultimo_acceso' => now()
        ]);
    }
    
    /**
     * Incrementar intentos fallidos
     */
    public function incrementarIntentosFallidos(int $id): void
    {
        $usuario = $this->findByIdOrFail($id);
        $usuario->incrementarIntentosFallidos();
    }
    
    /**
     * Resetear intentos fallidos
     */
    public function resetearIntentosFallidos(int $id): void
    {
        $usuario = $this->findByIdOrFail($id);
        $usuario->resetearIntentosFallidos();
    }
    
    /**
     * Verificar si usuario está bloqueado
     */
    public function estaBloqueado(int $id): bool
    {
        $usuario = $this->findByIdOrFail($id);
        return $usuario->estaBloqueado();
    }
}
