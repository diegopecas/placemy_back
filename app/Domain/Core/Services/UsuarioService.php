<?php

namespace App\Domain\Core\Services;

use App\Domain\Core\Repositories\UsuarioRepository;
use App\Domain\Core\Repositories\PersonaNaturalRepository;
use App\Domain\Core\Repositories\RolRepository;
use App\Domain\Core\Models\Usuario;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    protected $usuarioRepository;
    protected $personaRepository;
    protected $rolRepository;
    protected $auditoriaService;
    
    public function __construct(
        UsuarioRepository $usuarioRepository,
        PersonaNaturalRepository $personaRepository,
        RolRepository $rolRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->personaRepository = $personaRepository;
        $this->rolRepository = $rolRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear usuario con validaciones explícitas
     */
    public function crear(array $data): Usuario
    {
        // 1. Validar que la persona existe y está activa
        $persona = $this->personaRepository->findById($data['persona_id']);
        if (!$persona) {
            throw new BusinessException('Persona no encontrada');
        }
        if (!$persona->activo) {
            throw new BusinessException('La persona no está activa');
        }
        
        // 2. Validar que la persona no tiene usuario asignado
        if ($this->usuarioRepository->findByPersonaId($data['persona_id'])) {
            throw new BusinessException('Esta persona ya tiene un usuario asignado');
        }
        
        // 3. Validar username único
        if ($this->usuarioRepository->existeUsername($data['username'])) {
            throw new BusinessException('El username ya está en uso');
        }
        
        // 4. Validar email único
        if ($this->usuarioRepository->existeEmail($data['email'])) {
            throw new BusinessException('El email ya está registrado');
        }
        
        // 5. Hash del password
        $data['password'] = Hash::make($data['password']);
        
        DB::beginTransaction();
        try {
            // Crear usuario
            $usuario = $this->usuarioRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_usuarios',
                $usuario->id,
                'INSERT',
                auth()->id(),
                null,
                array_merge($usuario->toArray(), ['password' => '[OCULTO]'])
            );
            
            DB::commit();
            return $usuario;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar usuario
     */
    public function actualizar(int $id, array $data): Usuario
    {
        // Validar que el usuario existe
        $usuario = $this->usuarioRepository->findByIdOrFail($id);
        
        // Validar username único (excluyendo el actual)
        if (isset($data['username']) && $this->usuarioRepository->existeUsername($data['username'], $id)) {
            throw new BusinessException('El username ya está en uso por otro usuario');
        }
        
        // Validar email único (excluyendo el actual)
        if (isset($data['email']) && $this->usuarioRepository->existeEmail($data['email'], $id)) {
            throw new BusinessException('El email ya está registrado por otro usuario');
        }
        
        // Si se actualiza password, hacer hash
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $usuario->toArray();
            
            // Actualizar
            $usuarioActualizado = $this->usuarioRepository->update($id, $data);
            
            // Auditoría (ocultar password)
            $datosNuevos = $usuarioActualizado->toArray();
            if (isset($datosNuevos['password'])) {
                $datosNuevos['password'] = '[OCULTO]';
            }
            if (isset($datosAnteriores['password'])) {
                $datosAnteriores['password'] = '[OCULTO]';
            }
            
            $this->auditoriaService->registrar(
                'core_usuarios',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $datosNuevos
            );
            
            DB::commit();
            return $usuarioActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Asignar rol a usuario
     */
    public function asignarRol(int $usuarioId, int $rolId): void
    {
        // 1. Validar que usuario existe y está activo
        $usuario = $this->usuarioRepository->findById($usuarioId);
        if (!$usuario) {
            throw new BusinessException('Usuario no encontrado');
        }
        if (!$usuario->activo) {
            throw new BusinessException('Usuario no está activo');
        }
        
        // 2. Validar que rol existe y está activo
        $rol = $this->rolRepository->findById($rolId);
        if (!$rol) {
            throw new BusinessException('Rol no encontrado');
        }
        if (!$rol->activo) {
            throw new BusinessException('Rol no está activo');
        }
        
        DB::beginTransaction();
        try {
            // Asignar rol (si no lo tiene ya)
            if (!$usuario->roles()->where('rol_id', $rolId)->exists()) {
                $usuario->roles()->attach($rolId);
                
                // Auditoría
                $this->auditoriaService->registrar(
                    'core_usuarios_roles',
                    $usuarioId,
                    'INSERT',
                    auth()->id(),
                    null,
                    ['usuario_id' => $usuarioId, 'rol_id' => $rolId]
                );
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Remover rol de usuario
     */
    public function removerRol(int $usuarioId, int $rolId): void
    {
        $usuario = $this->usuarioRepository->findByIdOrFail($usuarioId);
        
        DB::beginTransaction();
        try {
            $usuario->roles()->detach($rolId);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_usuarios_roles',
                $usuarioId,
                'DELETE',
                auth()->id(),
                ['usuario_id' => $usuarioId, 'rol_id' => $rolId],
                null
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener usuario por ID con relaciones
     */
    public function obtenerPorId(int $id): Usuario
    {
        return $this->usuarioRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Usuario no encontrado');
    }
    
    /**
     * Cambiar estado del usuario
     */
    public function cambiarEstado(int $id, bool $activo): Usuario
    {
        $usuario = $this->usuarioRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $usuario->toArray();
            
            $usuarioActualizado = $this->usuarioRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_usuarios',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $usuarioActualizado->toArray()
            );
            
            DB::commit();
            return $usuarioActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar password
     */
    public function cambiarPassword(int $id, string $passwordActual, string $passwordNuevo): bool
    {
        $usuario = $this->usuarioRepository->findByIdOrFail($id);
        
        // Verificar password actual
        if (!Hash::check($passwordActual, $usuario->password)) {
            throw new BusinessException('Password actual incorrecto');
        }
        
        DB::beginTransaction();
        try {
            $this->usuarioRepository->update($id, [
                'password' => Hash::make($passwordNuevo)
            ]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_usuarios',
                $id,
                'UPDATE',
                auth()->id(),
                ['accion' => 'Cambio de password'],
                ['accion' => 'Cambio de password']
            );
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
