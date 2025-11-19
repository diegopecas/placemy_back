<?php

namespace App\Domain\Auth\Services;

use App\Domain\Core\Repositories\UsuarioRepository;
use App\Domain\Core\Models\Usuario;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use App\Domain\Establecimiento\Models\Establecimiento;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthService
{
    protected $usuarioRepository;
    protected $auditoriaService;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->auditoriaService = $auditoriaService;
    }

    /**
     * Login de usuario
     */
    public function login(string $identifier, string $password): array
    {
        // 1. Buscar usuario por username o email
        $usuario = $this->usuarioRepository->findByUsernameOrEmail($identifier);

        if (!$usuario) {
            throw new BusinessException('Credenciales incorrectas', 401);
        }

        // 2. Validar que el usuario está activo
        if (!$usuario->estaActivo()) {
            throw new BusinessException('Usuario inactivo. Contacte al administrador', 403);
        }

        // 3. Validar que no está bloqueado
        if ($usuario->estaBloqueado()) {
            $minutosRestantes = now()->diffInMinutes($usuario->bloqueado_hasta);
            throw new BusinessException(
                "Usuario bloqueado temporalmente. Intente nuevamente en {$minutosRestantes} minutos",
                403
            );
        }

        // 4. Validar password
        if (!Hash::check($password, $usuario->password)) {
            // Incrementar intentos fallidos
            $usuario->incrementarIntentosFallidos();

            // Auditoría de intento fallido
            $this->auditoriaService->registrar(
                'core_usuarios',
                $usuario->id,
                'LOGIN_FAILED',
                $usuario->id,
                null,
                ['intentos_fallidos' => $usuario->intentos_fallidos]
            );

            if ($usuario->intentos_fallidos >= 5) {
                throw new BusinessException('Usuario bloqueado por múltiples intentos fallidos', 403);
            }

            throw new BusinessException('Credenciales incorrectas', 401);
        }

        // 5. Login exitoso - Resetear intentos fallidos
        $usuario->resetearIntentosFallidos();

        // 6. Actualizar último acceso
        $this->usuarioRepository->actualizarUltimoAcceso($usuario->id);

        // 7. Generar tokens
        $accessToken = $usuario->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;
        $refreshToken = $usuario->createToken('refresh_token', ['refresh'], now()->addDays(30))->plainTextToken;

        // 8. Cargar relaciones
        $usuario->load(['persona']);

        // 9. Obtener establecimientos del usuario
        $establecimientos = $this->getEstablecimientosUsuario($usuario);

        // 10. Auditoría de login exitoso
        $this->auditoriaService->registrar(
            'core_usuarios',
            $usuario->id,
            'LOGIN_SUCCESS',
            $usuario->id
        );

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => 900, // 15 minutos en segundos
            'user' => [
                'id' => $usuario->id,
                'username' => $usuario->username,
                'email' => $usuario->email,
                'persona' => $usuario->persona ? [
                    'id' => $usuario->persona->id,
                    'nombre_completo' => $usuario->persona->nombre_completo,
                    'numero_documento' => $usuario->persona->numero_documento,
                    'telefono' => $usuario->persona->telefono,
                    'email' => $usuario->persona->email,
                ] : null,
            ],
            'establecimientos' => $establecimientos,
        ];
    }

    /**
     * Obtener establecimientos donde el usuario tiene acceso con sus roles
     */
    private function getEstablecimientosUsuario(Usuario $usuario): array
    {
        $establecimientosIds = $usuario->getEstablecimientosIds();

        if (empty($establecimientosIds)) {
            return [];
        }

        $establecimientos = Establecimiento::whereIn('id', $establecimientosIds)
            ->where('activo', true)
            ->get();

        return $establecimientos->map(function ($establecimiento) use ($usuario) {
            $roles = $usuario->rolesEnEstablecimiento($establecimiento->id)
                ->with('permisos')
                ->get();

            return [
                'id' => $establecimiento->id,
                'nombre' => $establecimiento->nombre,
                'slug' => $establecimiento->slug,
                'logo_url' => $establecimiento->logo_url,
                'roles' => $roles->map(function ($rol) {
                    return [
                        'id' => $rol->id,
                        'nombre' => $rol->nombre,
                        'permisos' => $rol->permisos->pluck('codigo')->toArray(),
                    ];
                })->toArray(),
            ];
        })->toArray();
    }

    /**
     * Obtener permisos del usuario en un establecimiento específico
     */
    public function getPermisosEnEstablecimiento(Usuario $usuario, int $establecimientoId): array
    {
        $roles = $usuario->rolesEnEstablecimiento($establecimientoId)
            ->with('permisos')
            ->get();

        return [
            'establecimiento_id' => $establecimientoId,
            'roles' => $roles->map(function ($rol) {
                return [
                    'id' => $rol->id,
                    'nombre' => $rol->nombre,
                    'permisos' => $rol->permisos->pluck('codigo')->toArray(),
                ];
            }),
            'permisos' => $usuario->getAllPermissionsInEstablecimiento($establecimientoId),
        ];
    }

    /**
     * Refresh token - Generar nuevo access token
     */
    public function refreshToken(Usuario $usuario): array
    {
        // Validar que el usuario está activo
        if (!$usuario->estaActivo()) {
            throw new BusinessException('Usuario inactivo', 403);
        }

        // Revocar tokens anteriores
        $usuario->tokens()->where('name', 'access_token')->delete();

        // Generar nuevo access token
        $accessToken = $usuario->createToken('access_token', ['*'], now()->addMinutes(15))->plainTextToken;

        return [
            'access_token' => $accessToken,
            'token_type' => 'Bearer',
            'expires_in' => 900,
        ];
    }

    /**
     * Logout - Revocar todos los tokens del usuario
     */
    public function logout(Usuario $usuario): void
    {
        // Revocar todos los tokens
        $usuario->tokens()->delete();

        // Auditoría
        $this->auditoriaService->registrar(
            'core_usuarios',
            $usuario->id,
            'LOGOUT',
            $usuario->id
        );
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Usuario $usuario): array
    {
        $usuario->load(['persona']);

        // Obtener establecimientos del usuario
        $establecimientos = $this->getEstablecimientosUsuario($usuario);

        return [
            'id' => $usuario->id,
            'username' => $usuario->username,
            'email' => $usuario->email,
            'activo' => $usuario->activo,
            'ultimo_acceso' => $usuario->ultimo_acceso,
            'persona' => $usuario->persona ? [
                'id' => $usuario->persona->id,
                'nombre_completo' => $usuario->persona->nombre_completo,
                'numero_documento' => $usuario->persona->numero_documento,
                'telefono' => $usuario->persona->telefono,
                'email' => $usuario->persona->email,
            ] : null,
            'establecimientos' => $establecimientos,
        ];
    }
}
