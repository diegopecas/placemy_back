<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Services\ClienteFechaEspecialService;
use App\Domain\Cliente\Requests\CreateClienteFechaEspecialRequest;
use App\Domain\Cliente\Requests\UpdateClienteFechaEspecialRequest;
use Illuminate\Http\JsonResponse;

class ClienteFechaEspecialController extends Controller
{
    protected $clienteFechaEspecialService;
    
    public function __construct(ClienteFechaEspecialService $clienteFechaEspecialService)
    {
        $this->clienteFechaEspecialService = $clienteFechaEspecialService;
    }
    
    /**
     * Listar fechas especiales de un cliente
     * GET /api/cliente/clientes/{clienteId}/fechas-especiales
     */
    public function index(int $clienteId): JsonResponse
    {
        try {
            $fechas = $this->clienteFechaEspecialService->listarPorCliente($clienteId);
            
            return response()->json([
                'success' => true,
                'data' => $fechas
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener fecha especial por ID
     * GET /api/cliente/fechas-especiales/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $fecha = $this->clienteFechaEspecialService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $fecha
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Crear fecha especial
     * POST /api/cliente/clientes/{clienteId}/fechas-especiales
     */
    public function store(CreateClienteFechaEspecialRequest $request, int $clienteId): JsonResponse
    {
        try {
            $data = array_merge($request->validated(), ['cliente_id' => $clienteId]);
            $fecha = $this->clienteFechaEspecialService->crear($data);
            
            return response()->json([
                'success' => true,
                'message' => 'Fecha especial creada exitosamente',
                'data' => $fecha
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar fecha especial
     * PUT /api/cliente/fechas-especiales/{id}
     */
    public function update(UpdateClienteFechaEspecialRequest $request, int $id): JsonResponse
    {
        try {
            $fecha = $this->clienteFechaEspecialService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Fecha especial actualizada exitosamente',
                'data' => $fecha
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eliminar fecha especial
     * DELETE /api/cliente/fechas-especiales/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clienteFechaEspecialService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Fecha especial eliminada exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
