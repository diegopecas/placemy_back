<?php

namespace App\Domain\Cuenta\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cuenta\Contracts\CuentaInteraccionServiceInterface;
use App\Domain\Cuenta\Requests\CreateCuentaInteraccionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CuentaInteraccionController extends Controller
{
    protected $interaccionService;
    
    public function __construct(CuentaInteraccionServiceInterface $interaccionService)
    {
        $this->interaccionService = $interaccionService;
    }
    
    /**
     * Listar interacciones de una cuenta
     */
    public function index(int $cuentaId): JsonResponse
    {
        try {
            $interacciones = $this->interaccionService->listarPorCuenta($cuentaId);
            
            return response()->json([
                'success' => true,
                'data' => $interacciones
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Listar interacciones pendientes por establecimiento
     */
    public function pendientes(int $establecimientoId): JsonResponse
    {
        try {
            $interacciones = $this->interaccionService->listarPendientesPorEstablecimiento($establecimientoId);
            
            return response()->json([
                'success' => true,
                'data' => $interacciones
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener interacción por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $interaccion = $this->interaccionService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $interaccion
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear interacción
     */
    public function store(CreateCuentaInteraccionRequest $request): JsonResponse
    {
        try {
            $interaccion = $this->interaccionService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Interacción registrada exitosamente',
                'data' => $interaccion
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Atender interacción
     */
    public function atender(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'staff_id' => 'required|integer|exists:establecimiento_staff,id',
                'notas' => 'nullable|string'
            ]);
            
            $interaccion = $this->interaccionService->atender(
                $id,
                $request->input('staff_id'),
                $request->input('notas')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Interacción atendida exitosamente',
                'data' => $interaccion
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado de interacción
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'estado_id' => 'required|integer|exists:interaccion_estados,id'
            ]);
            
            $interaccion = $this->interaccionService->cambiarEstado($id, $request->input('estado_id'));
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de interacción actualizado exitosamente',
                'data' => $interaccion
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
