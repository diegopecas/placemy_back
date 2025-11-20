<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Contracts\ClienteServiceInterface;
use App\Domain\Cliente\Requests\CreateClienteRequest;
use App\Domain\Cliente\Requests\UpdateClienteRequest;
use App\Domain\Cliente\Requests\CreateClienteCompletoRequest;
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
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener cliente por ID
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
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear cliente básico
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
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear cliente completo (orquestador)
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
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar cliente
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
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Eliminar cliente
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
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
