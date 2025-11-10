<?php

namespace App\Domain\Restaurante\Controllers;

use App\Http\Controllers\Controller;
use App\Domain\Restaurante\Models\TipoCocina;
use App\Domain\Restaurante\Models\RangoPrecio;
use App\Domain\Restaurante\Models\MetodoPago;
use App\Domain\Restaurante\Models\CaracteristicaRestaurante;
use App\Domain\Restaurante\Models\EstadoMesa;
use App\Domain\Restaurante\Models\Alergeno;
use App\Domain\Restaurante\Models\Cargo;
use Illuminate\Http\JsonResponse;

class CatalogoController extends Controller
{
    /**
     * Obtener todos los catálogos del dominio Restaurante
     * Endpoint: GET /api/restaurante/catalogos
     * 
     * Este endpoint está disponible para cualquier usuario autenticado
     * y retorna todos los catálogos necesarios para los formularios.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'tipos_cocina' => TipoCocina::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'descripcion']),
                
                'rangos_precio' => RangoPrecio::where('activo', true)
                    ->orderBy('orden')
                    ->get(['id', 'nombre', 'rango_inferior', 'rango_superior', 'simbolo']),
                
                'metodos_pago' => MetodoPago::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'descripcion', 'icono']),
                
                'caracteristicas_restaurante' => CaracteristicaRestaurante::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'descripcion', 'icono']),
                
                'estados_mesa' => EstadoMesa::orderBy('nombre')
                    ->get(['id', 'nombre', 'descripcion', 'color']),
                
                'alergenos' => Alergeno::where('activo', true)
                    ->orderBy('nombre')
                    ->get(['id', 'nombre', 'descripcion', 'icono']),
                
                'cargos' => Cargo::where('activo', true)
                    ->orderBy('nivel_jerarquico')
                    ->get(['id', 'nombre', 'descripcion', 'nivel_jerarquico']),
            ]
        ], 200);
    }
    
    /**
     * Obtener un catálogo específico
     * Endpoint: GET /api/restaurante/catalogos/{tipo}
     * 
     * @param string $tipo tipos_cocina|rangos_precio|metodos_pago|caracteristicas|estados_mesa|alergenos|cargos
     */
    public function show(string $tipo): JsonResponse
    {
        $catalogos = [
            'tipos_cocina' => TipoCocina::where('activo', true)->orderBy('nombre')->get(),
            'rangos_precio' => RangoPrecio::where('activo', true)->orderBy('orden')->get(),
            'metodos_pago' => MetodoPago::where('activo', true)->orderBy('nombre')->get(),
            'caracteristicas' => CaracteristicaRestaurante::where('activo', true)->orderBy('nombre')->get(),
            'estados_mesa' => EstadoMesa::orderBy('nombre')->get(),
            'alergenos' => Alergeno::where('activo', true)->orderBy('nombre')->get(),
            'cargos' => Cargo::where('activo', true)->orderBy('nivel_jerarquico')->get(),
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
