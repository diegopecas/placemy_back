<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Services\ClienteAlergenoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteAlergenoController extends Controller
{
    protected $clienteAlergenoService;
    
    public function __construct(ClienteAlergenoService $clienteAlergenoService)
    {
        $this->clienteAlergenoService = $clienteAlergenoService;
    }
    
    /**
     * Listar alérgenos de un cliente
     * GET /api/cliente/clientes/{clienteId}/alergenos
     */
    public function index(int $clienteId): JsonResponse
    {
        try {
            $alergenos = $this->clienteAlergenoService->listar($clienteId);
            
            return response()->json([
                'success' => true,
                'data' => $alergenos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Agregar alérgeno a cliente
     * POST /api/cliente/clientes/{clienteId}/alergenos
     * Body: { "alergeno_id": 1 }
     */
    public function store(Request $request, int $clienteId): JsonResponse
    {
        try {
            $request->validate([
                'alergeno_id' => 'required|integer|exists:alergenos,id'
            ]);
            
            $alergenos = $this->clienteAlergenoService->agregar(
                $clienteId,
                $request->input('alergeno_id')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Alérgeno agregado exitosamente',
                'data' => $alergenos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Quitar alérgeno de cliente
     * DELETE /api/cliente/clientes/{clienteId}/alergenos/{alergenoId}
     */
    public function destroy(int $clienteId, int $alergenoId): JsonResponse
    {
        try {
            $this->clienteAlergenoService->quitar($clienteId, $alergenoId);
            
            return response()->json([
                'success' => true,
                'message' => 'Alérgeno eliminado exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Sincronizar alérgenos (reemplazar todos)
     * PUT /api/cliente/clientes/{clienteId}/alergenos
     * Body: { "alergenos": [1, 2, 3] }
     */
    public function sync(Request $request, int $clienteId): JsonResponse
    {
        try {
            $request->validate([
                'alergenos' => 'required|array',
                'alergenos.*' => 'integer|exists:alergenos,id'
            ]);
            
            $alergenos = $this->clienteAlergenoService->sincronizar(
                $clienteId,
                $request->input('alergenos')
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Alérgenos sincronizados exitosamente',
                'data' => $alergenos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
