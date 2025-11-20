<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Contracts\ResenaServiceInterface;
use App\Domain\Cliente\Requests\CreateResenaRequest;
use App\Domain\Cliente\Requests\UpdateResenaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    protected $resenaService;
    
    public function __construct(ResenaServiceInterface $resenaService)
    {
        $this->resenaService = $resenaService;
    }
    
    /**
     * Listar por establecimiento
     */
    public function indexPorEstablecimiento(Request $request, int $establecimientoId): JsonResponse
    {
        try {
            $filtros = $request->only(['calificacion', 'calificacion_minima', 'sin_respuesta']);
            $resenas = $this->resenaService->listarPorEstablecimiento($establecimientoId, $filtros);
            
            return response()->json([
                'success' => true,
                'data' => $resenas
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Listar por cliente-establecimiento
     */
    public function indexPorClienteEstablecimiento(int $clienteEstablecimientoId): JsonResponse
    {
        try {
            $resenas = $this->resenaService->listarPorClienteEstablecimiento($clienteEstablecimientoId);
            
            return response()->json([
                'success' => true,
                'data' => $resenas
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
            $resena = $this->resenaService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $resena
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear reseña
     */
    public function store(CreateResenaRequest $request): JsonResponse
    {
        try {
            $resena = $this->resenaService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Reseña creada exitosamente',
                'data' => $resena
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar reseña
     */
    public function update(UpdateResenaRequest $request, int $id): JsonResponse
    {
        try {
            $resena = $this->resenaService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Reseña actualizada exitosamente',
                'data' => $resena
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Responder reseña
     */
    public function responder(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'respuesta' => 'required|string|max:1000'
            ]);
            
            $resena = $this->resenaService->responder($id, $request->input('respuesta'));
            
            return response()->json([
                'success' => true,
                'message' => 'Respuesta agregada exitosamente',
                'data' => $resena
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Eliminar reseña
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->resenaService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Reseña eliminada exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
