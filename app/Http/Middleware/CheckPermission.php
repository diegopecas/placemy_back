<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Verificar si el usuario tiene el permiso requerido
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        
        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }
        
        // Verificar si el usuario tiene el permiso
        if (!$this->userHasPermission($user, $permission)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción',
                'required_permission' => $permission
            ], 403);
        }
        
        return $next($request);
    }
    
    /**
     * Verificar si el usuario tiene el permiso
     */
    protected function userHasPermission($user, string $permission): bool
    {
        // Obtener roles del usuario
        $roles = $user->roles;
        
        foreach ($roles as $rol) {
            // Obtener permisos del rol
            $permisos = $rol->permisos;
            
            foreach ($permisos as $permiso) {
                if ($permiso->codigo === $permission) {
                    return true;
                }
            }
        }
        
        return false;
    }
}
