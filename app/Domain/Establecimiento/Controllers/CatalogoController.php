<?php

namespace App\Domain\Establecimiento\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Establecimiento\Models\TipoCocina;
use App\Domain\Establecimiento\Models\RangoPrecio;
use App\Domain\Establecimiento\Models\MetodoPago;
use App\Domain\Establecimiento\Models\CaracteristicaEstablecimiento;
use App\Domain\Establecimiento\Models\EstadoMesa;
use App\Domain\Establecimiento\Models\Alergeno;
use App\Domain\Establecimiento\Models\Cargo;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    /**
     * Obtener todos los catálogos del dominio Establecimiento
     * Endpoint: GET /api/establecimiento/catalogos
     * 
     * Este endpoint está disponible para cualquier usuario autenticado
     * y retorna todos los catálogos necesarios para los formularios.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'tipos_cocina' => TipoCocina::orderBy('nombre')
                    ->get(['id', 'nombre']),
                
                'rangos_precio' => RangoPrecio::orderBy('id')
                    ->get(['id', 'nombre']),
                
                'metodos_pago' => MetodoPago::orderBy('nombre')
                    ->get(['id', 'nombre']),
                
                'caracteristicas_establecimiento' => CaracteristicaEstablecimiento::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'descripcion', 'icono']),
                
                'estados_mesa' => EstadoMesa::orderBy('nombre')
                    ->get(['id', 'nombre', 'icono', 'color']),
                
                'alergenos' => Alergeno::orderBy('nombre')
                    ->get(['id', 'nombre']),
            ]
        ], 200);
    }
    
    /**
     * Obtener un catálogo específico
     * Endpoint: GET /api/establecimiento/catalogos/{tipo}
     * 
     * @param string $tipo tipos_cocina|rangos_precio|metodos_pago|caracteristicas|estados_mesa|alergenos
     */
    public function show(string $tipo): JsonResponse
    {
        $catalogos = [
            'tipos_cocina' => TipoCocina::orderBy('nombre')->get(),
            'rangos_precio' => RangoPrecio::orderBy('id')->get(),
            'metodos_pago' => MetodoPago::orderBy('nombre')->get(),
            'caracteristicas' => CaracteristicaEstablecimiento::where('activo', true)->orderBy('nombre')->get(),
            'estados_mesa' => EstadoMesa::orderBy('nombre')->get(),
            'alergenos' => Alergeno::orderBy('nombre')->get(),
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
    
    /**
     * Obtener cargos por establecimiento
     * Endpoint: GET /api/establecimiento/{establecimientoId}/cargos
     */
    public function cargosPorEstablecimiento(int $establecimientoId): JsonResponse
    {
        $cargos = Cargo::where('establecimiento_id', $establecimientoId)
            ->orderBy('nombre')
            ->get(['id', 'nombre']);
        
        return response()->json([
            'success' => true,
            'data' => $cargos
        ], 200);
    }
}
