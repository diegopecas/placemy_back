<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar permisos por establecimiento
 * 
 * Uso en rutas:
 * Route::middleware(['auth:sanctum', 'permission:mesas.ver'])
 */
class CheckPermission
{
    /**
     * Verificar si el usuario tiene el permiso requerido en el establecimiento
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        
        // 1. Verificar autenticación
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado'
            ], 401);
        }
        
        // 2. Obtener establecimiento_id del request
        $establecimientoId = $this->getEstablecimientoId($request);
        
        if (!$establecimientoId) {
            return response()->json([
                'success' => false,
                'message' => 'El establecimiento_id es requerido para esta operación',
                'required_permission' => $permission
            ], 400);
        }
        
        // 3. Verificar que el establecimiento_id sea numérico y válido
        if (!is_numeric($establecimientoId) || $establecimientoId <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'El establecimiento_id debe ser un número válido',
                'required_permission' => $permission
            ], 400);
        }
        
        // 4. Verificar si el usuario tiene acceso al establecimiento
        $establecimientosIds = $user->getEstablecimientosIds();
        if (!in_array((int)$establecimientoId, $establecimientosIds)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes acceso a este establecimiento',
                'required_permission' => $permission
            ], 403);
        }
        
        // 5. Verificar si el usuario tiene el permiso en este establecimiento
        if (!$user->hasPermissionInEstablecimiento($permission, (int)$establecimientoId)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para realizar esta acción en este establecimiento',
                'required_permission' => $permission,
                'establecimiento_id' => $establecimientoId
            ], 403);
        }
        
        return $next($request);
    }
    
    /**
     * Obtener establecimiento_id del request
     * Busca en: query params, body, route params
     */
    protected function getEstablecimientoId(Request $request): int|string|null
    {
        // 1. Buscar en query params (?establecimiento_id=1)
        if ($request->has('establecimiento_id')) {
            return $request->query('establecimiento_id');
        }
        
        // 2. Buscar en body (POST/PUT/PATCH)
        if ($request->has('establecimiento_id')) {
            return $request->input('establecimiento_id');
        }
        
        // 3. Buscar en route params (/establecimientos/{establecimientoId}/staff)
        if ($request->route('establecimientoId')) {
            return $request->route('establecimientoId');
        }
        
        // 4. Buscar en route params alternativo
        if ($request->route('establecimiento_id')) {
            return $request->route('establecimiento_id');
        }
        
        return null;
    }
}