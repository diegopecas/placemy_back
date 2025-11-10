<?php

namespace App\Domain\Restaurante\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Restaurante\Contracts\ProductoServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    protected $productoService;
    
    public function __construct(ProductoServiceInterface $productoService)
    {
        $this->productoService = $productoService;
    }
    
    /**
     * Listar productos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $productos = $this->productoService->obtenerTodos();
            
            return response()->json([
                'success' => true,
                'data' => $productos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener producto por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $producto = $this->productoService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $producto
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear producto
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $producto = $this->productoService->crear($request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Producto creado exitosamente',
                'data' => $producto
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar producto
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $producto = $this->productoService->actualizar($id, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado exitosamente',
                'data' => $producto
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Asignar producto a restaurante
     */
    public function asignarARestaurante(Request $request, int $id): JsonResponse
    {
        try {
            $this->productoService->asignarARestaurante(
                $id,
                $request->input('restaurante_id'),
                $request->all()
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Producto asignado al restaurante exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar producto en restaurante
     */
    public function actualizarEnRestaurante(Request $request, int $id, int $restauranteId): JsonResponse
    {
        try {
            $this->productoService->actualizarEnRestaurante($id, $restauranteId, $request->all());
            
            return response()->json([
                'success' => true,
                'message' => 'Producto actualizado en restaurante exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Desasignar producto de restaurante
     */
    public function desasignarDeRestaurante(int $id, int $restauranteId): JsonResponse
    {
        try {
            $this->productoService->desasignarDeRestaurante($id, $restauranteId);
            
            return response()->json([
                'success' => true,
                'message' => 'Producto desasignado del restaurante exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
