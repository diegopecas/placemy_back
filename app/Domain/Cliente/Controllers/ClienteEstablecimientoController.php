<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Contracts\ClienteEstablecimientoServiceInterface;
use App\Domain\Cliente\Requests\CreateClienteEstablecimientoRequest;
use App\Domain\Cliente\Requests\UpdateClienteEstablecimientoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteEstablecimientoController extends Controller
{
    protected $clienteEstablecimientoService;
    
    public function __construct(ClienteEstablecimientoServiceInterface $clienteEstablecimientoService)
    {
        $this->clienteEstablecimientoService = $clienteEstablecimientoService;
    }
    
    /**
     * Listar por cliente
     */
    public function indexPorCliente(int $clienteId): JsonResponse
    {
        try {
            $asociaciones = $this->clienteEstablecimientoService->listarPorCliente($clienteId);
            
            return response()->json([
                'success' => true,
                'data' => $asociaciones
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Listar por establecimiento
     */
    public function indexPorEstablecimiento(Request $request, int $establecimientoId): JsonResponse
    {
        try {
            $filtros = $request->only(['busqueda', 'calificacion_minima']);
            $asociaciones = $this->clienteEstablecimientoService->listarPorEstablecimiento($establecimientoId, $filtros);
            
            return response()->json([
                'success' => true,
                'data' => $asociaciones
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
            $asociacion = $this->clienteEstablecimientoService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $asociacion
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear asociación
     */
    public function store(CreateClienteEstablecimientoRequest $request): JsonResponse
    {
        try {
            $asociacion = $this->clienteEstablecimientoService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente asociado al establecimiento exitosamente',
                'data' => $asociacion
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar asociación
     */
    public function update(UpdateClienteEstablecimientoRequest $request, int $id): JsonResponse
    {
        try {
            $asociacion = $this->clienteEstablecimientoService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Asociación actualizada exitosamente',
                'data' => $asociacion
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Eliminar asociación
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clienteEstablecimientoService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Asociación eliminada exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
