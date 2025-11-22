<?php

namespace App\Domain\Cuenta\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cuenta\Contracts\CuentaItemServiceInterface;
use App\Domain\Cuenta\Requests\CreateCuentaItemRequest;
use App\Domain\Cuenta\Requests\UpdateCuentaItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CuentaItemController extends Controller
{
    protected $cuentaItemService;
    
    public function __construct(CuentaItemServiceInterface $cuentaItemService)
    {
        $this->cuentaItemService = $cuentaItemService;
    }
    
    /**
     * Listar items de una cuenta
     */
    public function index(int $cuentaId): JsonResponse
    {
        try {
            $items = $this->cuentaItemService->listarPorCuenta($cuentaId);
            
            return response()->json([
                'success' => true,
                'data' => $items
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Listar items modificables de una cuenta
     */
    public function modificables(int $cuentaId): JsonResponse
    {
        try {
            $items = $this->cuentaItemService->listarModificables($cuentaId);
            
            return response()->json([
                'success' => true,
                'data' => $items
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener item por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $item = $this->cuentaItemService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $item
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear item en cuenta
     */
    public function store(CreateCuentaItemRequest $request): JsonResponse
    {
        try {
            $item = $this->cuentaItemService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Item agregado a la cuenta exitosamente',
                'data' => $item
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar item
     */
    public function update(UpdateCuentaItemRequest $request, int $id): JsonResponse
    {
        try {
            $item = $this->cuentaItemService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Item actualizado exitosamente',
                'data' => $item
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Eliminar item
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->cuentaItemService->eliminar($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Item eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado de item
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'estado_id' => 'required|integer|exists:cuenta_item_estados,id'
            ]);
            
            $item = $this->cuentaItemService->cambiarEstado($id, $request->input('estado_id'));
            
            return response()->json([
                'success' => true,
                'message' => 'Estado del item actualizado exitosamente',
                'data' => $item
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
