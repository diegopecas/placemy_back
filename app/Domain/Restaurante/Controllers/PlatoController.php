<?php

namespace App\Domain\Restaurante\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Restaurante\Contracts\PlatoServiceInterface;
use App\Domain\Restaurante\Requests\CreatePlatoRequest;
use App\Domain\Restaurante\Requests\UpdatePlatoRequest;
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
     * Asignar plato a restaurante
     */
    public function asignarARestaurante(Request $request, int $id): JsonResponse
    {
        try {
            $this->platoService->asignarARestaurante(
                $id,
                $request->input('restaurante_id'),
                $request->all()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Plato asignado al restaurante exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar plato en restaurante (precio, disponibilidad)
     */
    public function actualizarEnRestaurante(Request $request, int $id, int $restauranteId): JsonResponse
    {
        try {
            $this->platoService->actualizarEnRestaurante($id, $restauranteId, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Plato actualizado en restaurante exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Desasignar plato de restaurante
     */
    public function desasignarDeRestaurante(int $id, int $restauranteId): JsonResponse
    {
        try {
            $this->platoService->desasignarDeRestaurante($id, $restauranteId);
            
            return response()->json([
                'success' => true,
                'message' => 'Plato desasignado del restaurante exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
