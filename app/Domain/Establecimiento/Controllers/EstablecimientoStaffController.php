<?php

namespace App\Domain\Establecimiento\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Establecimiento\Contracts\EstablecimientoStaffServiceInterface;
use App\Domain\Establecimiento\Requests\CreateEstablecimientoStaffRequest;
use App\Domain\Establecimiento\Requests\UpdateEstablecimientoStaffRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstablecimientoStaffController extends Controller
{
    protected $staffService;
    
    public function __construct(EstablecimientoStaffServiceInterface $staffService)
    {
        $this->staffService = $staffService;
    }
    
    /**
     * Listar staff por establecimiento
     */
    public function index(Request $request, int $establecimientoId): JsonResponse
    {
        try {
            $filtros = $request->only(['cargo_id', 'activo', 'busqueda']);
            $staff = $this->staffService->listarPorEstablecimiento($establecimientoId, $filtros);
            
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
     * Crear staff en establecimiento
     */
    public function store(CreateEstablecimientoStaffRequest $request): JsonResponse
    {
        try {
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
    public function update(UpdateEstablecimientoStaffRequest $request, int $id): JsonResponse
    {
        try {
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
     * Eliminar staff (soft delete)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->staffService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Staff eliminado exitosamente'
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
    
    /**
     * Obtener staff por cargo en establecimiento
     */
    public function porCargo(int $establecimientoId, int $cargoId): JsonResponse
    {
        try {
            $staff = $this->staffService->obtenerPorCargo($establecimientoId, $cargoId);
            
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
}