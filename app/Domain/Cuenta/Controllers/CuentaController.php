<?php

namespace App\Domain\Cuenta\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cuenta\Contracts\CuentaServiceInterface;
use App\Domain\Cuenta\Requests\CreateCuentaRequest;
use App\Domain\Cuenta\Requests\UpdateCuentaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CuentaController extends Controller
{
    protected $cuentaService;
    
    public function __construct(CuentaServiceInterface $cuentaService)
    {
        $this->cuentaService = $cuentaService;
    }
    
    /**
     * Listar cuentas por establecimiento
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filtros = $request->only([
                'establecimiento_id',
                'estado_id',
                'mesa_id',
                'establecimiento_staff_id',
                'cliente_id',
                'fecha_desde',
                'fecha_hasta',
                'activo'
            ]);
            
            $cuentas = $this->cuentaService->listar($filtros);
            
            return response()->json([
                'success' => true,
                'data' => $cuentas
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener cuenta por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener cuenta por número
     */
    public function showByNumero(string $numeroCuenta): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->obtenerPorNumeroCuenta($numeroCuenta);
            
            return response()->json([
                'success' => true,
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener cuenta por palabra secreta
     */
    public function showByPalabraSecreta(string $palabraSecreta): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->obtenerPorPalabraSecreta($palabraSecreta);
            
            return response()->json([
                'success' => true,
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener cuenta activa de una mesa
     */
    public function showActivaMesa(int $mesaId): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->obtenerCuentaActivaMesa($mesaId);
            
            if (!$cuenta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay cuenta activa en esta mesa'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear cuenta
     */
    public function store(CreateCuentaRequest $request): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cuenta creada exitosamente',
                'data' => $cuenta
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar cuenta
     */
    public function update(UpdateCuentaRequest $request, int $id): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Cuenta actualizada exitosamente',
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado de cuenta
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'estado_id' => 'required|integer|exists:cuenta_estados,id'
            ]);
            
            $cuenta = $this->cuentaService->cambiarEstado($id, $request->input('estado_id'));
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de cuenta actualizado exitosamente',
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cerrar cuenta
     */
    public function cerrar(Request $request, int $id): JsonResponse
    {
        try {
            $request->validate([
                'staff_id' => 'required|integer|exists:establecimiento_staff,id'
            ]);
            
            $cuenta = $this->cuentaService->cerrarCuenta($id, $request->input('staff_id'));
            
            return response()->json([
                'success' => true,
                'message' => 'Cuenta cerrada exitosamente',
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Calcular totales
     */
    public function calcularTotales(int $id): JsonResponse
    {
        try {
            $cuenta = $this->cuentaService->calcularTotales($id);
            
            return response()->json([
                'success' => true,
                'message' => 'Totales calculados exitosamente',
                'data' => $cuenta
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
