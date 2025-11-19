<?php

namespace App\Domain\Establecimiento\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Establecimiento\Contracts\MesaServiceInterface;
use App\Domain\Establecimiento\Requests\CreateMesaRequest;
use App\Domain\Establecimiento\Requests\UpdateMesaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MesaController extends Controller
{
    protected $mesaService;
    
    public function __construct(MesaServiceInterface $mesaService)
    {
        $this->mesaService = $mesaService;
    }
    
    /**
     * Listar mesas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Filtrar por establecimiento si se proporciona
            $establecimientoId = $request->input('establecimiento_id');
            
            if ($establecimientoId) {
                $mesas = $this->mesaService->obtenerPorEstablecimiento($establecimientoId);
            } else {
                $mesas = $this->mesaService->obtenerTodas();
            }
            
            return response()->json([
                'success' => true,
                'data' => $mesas
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Obtener mesa por ID
     */
    public function show(int $id): JsonResponse
    {
        try {
            $mesa = $this->mesaService->obtenerPorId($id);
            
            return response()->json([
                'success' => true,
                'data' => $mesa
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Crear mesa
     */
    public function store(CreateMesaRequest $request): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $mesa = $this->mesaService->crear($request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Mesa creada exitosamente',
                'data' => $mesa
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Actualizar mesa
     */
    public function update(UpdateMesaRequest $request, int $id): JsonResponse
    {
        try {
            // Los datos ya vienen validados por el Request
            $mesa = $this->mesaService->actualizar($id, $request->validated());
            
            return response()->json([
                'success' => true,
                'message' => 'Mesa actualizada exitosamente',
                'data' => $mesa
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Cambiar estado de la mesa
     */
    public function cambiarEstado(Request $request, int $id): JsonResponse
    {
        try {
            $mesa = $this->mesaService->cambiarEstado(
                $id,
                $request->input('estado_id')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de mesa actualizado exitosamente',
                'data' => $mesa
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
    
    /**
     * Asignar staff a mesa
     */
    public function asignarStaff(Request $request, int $id): JsonResponse
    {
        try {
            $mesa = $this->mesaService->asignarStaff(
                $id,
                $request->input('staff_id')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Staff asignado exitosamente a la mesa',
                'data' => $mesa
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], is_numeric($e->getCode()) ? (int)$e->getCode() : 500);
        }
    }
}
