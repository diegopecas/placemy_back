<?php

namespace App\Domain\Cuenta\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Obtener estados de cuenta
     */
    public function estadosCuenta(): JsonResponse
    {
        try {
            $estados = DB::table('cuenta_estados')
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $estados
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener estados de items de cuenta
     */
    public function estadosCuentaItem(): JsonResponse
    {
        try {
            $estados = DB::table('cuenta_item_estados')
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $estados
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener tipos de impuestos
     */
    public function tiposImpuestos(): JsonResponse
    {
        try {
            $tipos = DB::table('tipos_impuestos')
                ->where('activo', true)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $tipos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener tipos de items
     */
    public function tiposItems(): JsonResponse
    {
        try {
            $tipos = DB::table('tipos_items')
                ->where('activo', true)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $tipos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener categorías de interacciones
     */
    public function categoriasInteracciones(): JsonResponse
    {
        try {
            $categorias = DB::table('categorias_interacciones')
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $categorias
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener tipos de interacciones por categoría
     */
    public function tiposInteracciones(int $categoriaId = null): JsonResponse
    {
        try {
            $query = DB::table('tipos_interacciones')
                ->join('categorias_interacciones', 'tipos_interacciones.categoria_interaccion_id', '=', 'categorias_interacciones.id')
                ->select('tipos_interacciones.*', 'categorias_interacciones.nombre as categoria_nombre')
                ->where('tipos_interacciones.activo', true);
            
            if ($categoriaId) {
                $query->where('tipos_interacciones.categoria_interaccion_id', $categoriaId);
            }
            
            $tipos = $query->orderBy('categorias_interacciones.orden')
                          ->orderBy('tipos_interacciones.orden')
                          ->get();
            
            return response()->json([
                'success' => true,
                'data' => $tipos
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Obtener estados de interacciones
     */
    public function estadosInteracciones(): JsonResponse
    {
        try {
            $estados = DB::table('interaccion_estados')
                ->where('activo', true)
                ->orderBy('orden')
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $estados
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
