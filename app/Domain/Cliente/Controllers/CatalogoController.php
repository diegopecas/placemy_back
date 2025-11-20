<?php

namespace App\Domain\Cliente\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Cliente\Models\CanalContacto;
use App\Domain\Cliente\Models\TipoFechaEspecial;
use App\Domain\Cliente\Models\TipoCampania;
use App\Domain\Cliente\Models\TipoMovimiento;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    /**
     * Obtener todos los catálogos del dominio Cliente
     * Endpoint: GET /api/cliente/catalogos
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'canales_contacto' => CanalContacto::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'codigo', 'icono']),
                
                'tipos_fecha_especial' => TipoFechaEspecial::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'codigo', 'icono']),
                
                'tipos_campania' => TipoCampania::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'codigo', 'descripcion']),
                
                'tipos_movimiento' => TipoMovimiento::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'codigo', 'descripcion']),
            ]
        ], 200);
    }
    
    /**
     * Obtener un catálogo específico
     * Endpoint: GET /api/cliente/catalogos/{tipo}
     * 
     * @param string $tipo canales_contacto|tipos_fecha_especial|tipos_campania|tipos_movimiento
     */
    public function show(string $tipo): JsonResponse
    {
        $catalogos = [
            'canales_contacto' => CanalContacto::where('activo', true)->orderBy('nombre')->get(),
            'tipos_fecha_especial' => TipoFechaEspecial::where('activo', true)->orderBy('nombre')->get(),
            'tipos_campania' => TipoCampania::where('activo', true)->orderBy('nombre')->get(),
            'tipos_movimiento' => TipoMovimiento::where('activo', true)->orderBy('nombre')->get(),
        ];
        
        if (!isset($catalogos[$tipo])) {
            return response()->json([
                'success' => false,
                'message' => 'Catálogo no encontrado',
                'catalogos_disponibles' => array_keys($catalogos)
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $catalogos[$tipo]
        ], 200);
    }
}
