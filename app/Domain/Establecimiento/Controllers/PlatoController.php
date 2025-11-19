<?php

namespace App\Domain\Establecimiento\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Establecimiento\Contracts\PlatoServiceInterface;
use App\Domain\Establecimiento\Requests\CreatePlatoRequest;
use App\Domain\Establecimiento\Requests\UpdatePlatoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatoController extends Controller
{
    protected $platoService;
    
    public function __construct(PlatoServiceInterface $platoService)
    {
        $this->platoService = $platoService;
    }
    
    /**
     * Listar platos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Aquí puedes agregar filtros
            $platos = $this->platoService->obtenerTodos();
            
            return response()->json([
                'success' => true,
                'data' => $platos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener plato por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $plato = $this->platoService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $plato
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear plato
     */
    public function store(CreatePlatoRequest $request): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $plato = $this->platoService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Plato creado exitosamente',
                'data' => $plato
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar plato
     */
    public function update(UpdatePlatoRequest $request, int $id): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $plato = $this->platoService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Plato actualizado exitosamente',
                'data' => $plato
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Asignar plato a establecimiento
     */
    public function asignarAEstablecimiento(Request $request, int $id): JsonResponse
    {
        try {
            $this->platoService->asignarAEstablecimiento(
                $id,
                $request->input('establecimiento_id'),
                $request->all()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Plato asignado al establecimiento exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar plato en establecimiento (precio, disponibilidad)
     */
    public function actualizarEnEstablecimiento(Request $request, int $id, int $establecimientoId): JsonResponse
    {
        try {
            $this->platoService->actualizarEnEstablecimiento($id, $establecimientoId, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Plato actualizado en establecimiento exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Desasignar plato de establecimiento
     */
    public function desasignarDeEstablecimiento(int $id, int $establecimientoId): JsonResponse
    {
        try {
            $this->platoService->desasignarDeEstablecimiento($id, $establecimientoId);
            
            return response()->json([
                'success' => true,
                'message' => 'Plato desasignado del establecimiento exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
