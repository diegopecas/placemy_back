<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Contracts\ClienteServiceInterface;
use App\Domain\Cliente\Requests\CreateClienteRequest;
use App\Domain\Cliente\Requests\UpdateClienteRequest;
use App\Domain\Cliente\Requests\CreateClienteCompletoRequest;
use App\Domain\Cliente\Requests\UpdateClienteCompletoRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    protected $clienteService;
    
    public function __construct(ClienteServiceInterface $clienteService)
    {
        $this->clienteService = $clienteService;
    }
    
    /**
     * Listar clientes
     * GET /api/cliente/clientes
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only(['busqueda']);
            $clientes = $this->clienteService->listar($filtros);
            
            return response()->json([
                'success' => true,
                'data' => $clientes
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener cliente por ID
     * GET /api/cliente/clientes/{id}
     */
    public function show(int $id): JsonResponse
    {
        try {
            $cliente = $this->clienteService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $cliente
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 404);
        }
    }
    
    /**
     * Crear cliente básico (sin relaciones)
     * POST /api/cliente/clientes
     */
    public function store(CreateClienteRequest $request): JsonResponse
    {
        try {
            $cliente = $this->clienteService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente creado exitosamente',
                'data' => $cliente
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Crear cliente completo (orquestador)
     * POST /api/cliente/clientes/completo
     * Crea: Cliente + Alérgenos + Fechas Especiales
     */
    public function storeCompleto(CreateClienteCompletoRequest $request): JsonResponse
    {
        try {
            $cliente = $this->clienteService->crearCompleto($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente completo creado exitosamente',
                'data' => $cliente
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar cliente básico (sin relaciones)
     * PUT /api/cliente/clientes/{id}
     */
    public function update(UpdateClienteRequest $request, int $id): JsonResponse
    {
        try {
            $cliente = $this->clienteService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente actualizado exitosamente',
                'data' => $cliente
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Actualizar cliente completo (orquestador)
     * PUT /api/cliente/clientes/{id}/completo
     * Actualiza: Cliente + PersonaNatural + Sync Alérgenos + Fechas Especiales
     */
    public function updateCompleto(UpdateClienteCompletoRequest $request, int $id): JsonResponse
    {
        try {
            $cliente = $this->clienteService->actualizarCompleto($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente completo actualizado exitosamente',
                'data' => $cliente
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eliminar cliente
     * DELETE /api/cliente/clientes/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->clienteService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Cliente eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
