<?php

namespace App\Domain\Restaurante\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Restaurante\Contracts\RestauranteServiceInterface;
use App\Domain\Restaurante\Requests\CreateRestauranteRequest;
use App\Domain\Restaurante\Requests\UpdateRestauranteRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestauranteController extends Controller
{
    protected $restauranteService;
    
    public function __construct(RestauranteServiceInterface $restauranteService)
    {
        $this->restauranteService = $restauranteService;
    }
    
    /**
     * Listar restaurantes
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Aquí puedes agregar filtros desde el request
            $restaurantes = $this->restauranteService->obtenerTodos();
            
            return response()->json([
                'success' => true,
                'data' => $restaurantes
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener restaurante por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $restaurante = $this->restauranteService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $restaurante
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener restaurante por slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $restaurante = $this->restauranteService->obtenerPorSlug($slug);
            
            return response()->json([
                'success' => true,
                'data' => $restaurante
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear restaurante
     */
    public function store(CreateRestauranteRequest $request): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $restaurante = $this->restauranteService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurante creado exitosamente',
                'data' => $restaurante
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar restaurante
     */
    public function update(UpdateRestauranteRequest $request, int $id): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $restaurante = $this->restauranteService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurante actualizado exitosamente',
                'data' => $restaurante
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado del restaurante
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $restaurante = $this->restauranteService->cambiarEstado(
                $id,
                $request->input('activo', true)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $restaurante
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Verificar restaurante
     */
    public function verificar(Request $request, int $id): JsonResponse
    {
        try {
            $restaurante = $this->restauranteService->verificar(
                $id,
                $request->input('verificado', true)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Restaurante verificado exitosamente',
                'data' => $restaurante
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
