<?php

namespace App\Domain\Establecimiento\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Establecimiento\Contracts\EstablecimientoServiceInterface;
use App\Domain\Establecimiento\Requests\CreateEstablecimientoRequest;
use App\Domain\Establecimiento\Requests\UpdateEstablecimientoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EstablecimientoController extends Controller
{
    protected $establecimientoService;
    
    public function __construct(EstablecimientoServiceInterface $establecimientoService)
    {
        $this->establecimientoService = $establecimientoService;
    }
    
    /**
     * Listar establecimientos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Aquí puedes agregar filtros desde el request
            $establecimientos = $this->establecimientoService->obtenerTodos();
            
            return response()->json([
                'success' => true,
                'data' => $establecimientos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener establecimiento por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $establecimiento = $this->establecimientoService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $establecimiento
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener establecimiento por slug
     */
    public function showBySlug(string $slug): JsonResponse
    {
        try {
            $establecimiento = $this->establecimientoService->obtenerPorSlug($slug);
            
            return response()->json([
                'success' => true,
                'data' => $establecimiento
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear establecimiento
     */
    public function store(CreateEstablecimientoRequest $request): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $establecimiento = $this->establecimientoService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Establecimiento creado exitosamente',
                'data' => $establecimiento
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar establecimiento
     */
    public function update(UpdateEstablecimientoRequest $request, int $id): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $establecimiento = $this->establecimientoService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Establecimiento actualizado exitosamente',
                'data' => $establecimiento
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado del establecimiento
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $establecimiento = $this->establecimientoService->cambiarEstado(
                $id,
                $request->input('activo', true)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
                'data' => $establecimiento
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Verificar establecimiento
     */
    public function verificar(Request $request, int $id): JsonResponse
    {
        try {
            $establecimiento = $this->establecimientoService->verificar(
                $id,
                $request->input('verificado', true)
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Establecimiento verificado exitosamente',
                'data' => $establecimiento
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
