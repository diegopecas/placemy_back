<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Contracts\CampaniaServiceInterface;
use App\Domain\Cliente\Requests\CreateCampaniaRequest;
use App\Domain\Cliente\Requests\UpdateCampaniaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CampaniaController extends Controller
{
    protected $campaniaService;
    
    public function __construct(CampaniaServiceInterface $campaniaService)
    {
        $this->campaniaService = $campaniaService;
    }
    
    /**
     * Listar por establecimiento
     */
    public function index(Request $request, int $establecimientoId): JsonResponse
    {
        try {
            $filtros = $request->only(['activo', 'tipo_campania_id', 'vigente']);
            $campanias = $this->campaniaService->listarPorEstablecimiento($establecimientoId, $filtros);
            
            return response()->json([
                'success' => true,
                'data' => $campanias
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $campania = $this->campaniaService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $campania
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear campaña
     */
    public function store(CreateCampaniaRequest $request): JsonResponse
    {
        try {
            $campania = $this->campaniaService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Campaña creada exitosamente',
                'data' => $campania
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar campaña
     */
    public function update(UpdateCampaniaRequest $request, int $id): JsonResponse
    {
        try {
            $campania = $this->campaniaService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Campaña actualizada exitosamente',
                'data' => $campania
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Eliminar campaña
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->campaniaService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Campaña eliminada exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $campania = $this->campaniaService->cambiarEstado(
                $id,
                $request->input('activo', true)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de campaña actualizado exitosamente',
                'data' => $campania
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
