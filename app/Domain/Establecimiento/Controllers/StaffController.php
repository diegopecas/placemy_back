<?php

namespace App\Domain\Establecimiento\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Establecimiento\Contracts\StaffServiceInterface;
use App\Domain\Establecimiento\Requests\CreateStaffRequest;
use App\Domain\Establecimiento\Requests\UpdateStaffRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    protected $staffService;
    
    public function __construct(StaffServiceInterface $staffService)
    {
        $this->staffService = $staffService;
    }
    
    /**
     * Listar staff
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $staff = $this->staffService->obtenerTodos();
            
            return response()->json([
                'success' => true,
                'data' => $staff
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener staff por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $staff = $this->staffService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $staff
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear staff
     */
    public function store(CreateStaffRequest $request): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $staff = $this->staffService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Staff creado exitosamente',
                'data' => $staff
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar staff
     */
    public function update(UpdateStaffRequest $request, int $id): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $staff = $this->staffService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Staff actualizado exitosamente',
                'data' => $staff
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Asignar staff a establecimiento
     */
    public function asignarAEstablecimiento(Request $request, int $id): JsonResponse
    {
        try {
            $this->staffService->asignarAEstablecimiento(
                $id,
                $request->input('establecimiento_id'),
                $request->all()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Staff asignado al establecimiento exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar staff en establecimiento
     */
    public function actualizarEnEstablecimiento(Request $request, int $id, int $establecimientoId): JsonResponse
    {
        try {
            $this->staffService->actualizarEnEstablecimiento($id, $establecimientoId, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Staff actualizado en establecimiento exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Desasignar staff de establecimiento
     */
    public function desasignarDeEstablecimiento(int $id, int $establecimientoId): JsonResponse
    {
        try {
            $this->staffService->desasignarDeEstablecimiento($id, $establecimientoId);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff desasignado del establecimiento exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado del staff
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $staff = $this->staffService->cambiarEstado(
                $id,
                $request->input('activo', true)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Estado del staff actualizado exitosamente',
                'data' => $staff
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
