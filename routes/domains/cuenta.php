<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Cuenta\Controllers\CuentaController;
use App\Domain\Cuenta\Controllers\CuentaItemController;
use App\Domain\Cuenta\Controllers\CuentaInteraccionController;
use App\Domain\Cuenta\Controllers\CatalogoController;

Route::prefix('cuentas')->group(function () {
    
    // =====================================================
    // CATÁLOGOS (públicos)
    // =====================================================
    Route::get('/catalogos/estados', [CatalogoController::class, 'estadosCuenta']);
    Route::get('/catalogos/estados-item', [CatalogoController::class, 'estadosCuentaItem']);
    Route::get('/catalogos/tipos-impuestos', [CatalogoController::class, 'tiposImpuestos']);
    Route::get('/catalogos/tipos-items', [CatalogoController::class, 'tiposItems']);
    Route::get('/catalogos/categorias-interacciones', [CatalogoController::class, 'categoriasInteracciones']);
    Route::get('/catalogos/tipos-interacciones/{categoriaId?}', [CatalogoController::class, 'tiposInteracciones']);
    Route::get('/catalogos/estados-interacciones', [CatalogoController::class, 'estadosInteracciones']);
    
    // =====================================================
    // CUENTAS (protegidas con auth)
    // =====================================================
    Route::middleware('auth:sanctum')->group(function () {
        
        // Cuentas
        Route::get('/', [CuentaController::class, 'index']);
        Route::get('/{id}', [CuentaController::class, 'show']);
        Route::get('/numero/{numeroCuenta}', [CuentaController::class, 'showByNumero']);
        Route::get('/palabra/{palabraSecreta}', [CuentaController::class, 'showByPalabraSecreta']);
        Route::get('/mesa/{mesaId}/activa', [CuentaController::class, 'showActivaMesa']);
        Route::post('/', [CuentaController::class, 'store']);
        Route::put('/{id}', [CuentaController::class, 'update']);
        Route::patch('/{id}/estado', [CuentaController::class, 'cambiarEstado']);
        Route::patch('/{id}/cerrar', [CuentaController::class, 'cerrar']);
        Route::post('/{id}/calcular-totales', [CuentaController::class, 'calcularTotales']);
        
        // Items de cuenta
        Route::get('/{cuentaId}/items', [CuentaItemController::class, 'index']);
        Route::get('/{cuentaId}/items/modificables', [CuentaItemController::class, 'modificables']);
        Route::get('/items/{id}', [CuentaItemController::class, 'show']);
        Route::post('/items', [CuentaItemController::class, 'store']);
        Route::put('/items/{id}', [CuentaItemController::class, 'update']);
        Route::delete('/items/{id}', [CuentaItemController::class, 'destroy']);
        Route::patch('/items/{id}/estado', [CuentaItemController::class, 'cambiarEstado']);
        
        // Interacciones
        Route::get('/{cuentaId}/interacciones', [CuentaInteraccionController::class, 'index']);
        Route::get('/interacciones/pendientes/establecimiento/{establecimientoId}', [CuentaInteraccionController::class, 'pendientes']);
        Route::get('/interacciones/{id}', [CuentaInteraccionController::class, 'show']);
        Route::post('/interacciones', [CuentaInteraccionController::class, 'store']);
        Route::patch('/interacciones/{id}/atender', [CuentaInteraccionController::class, 'atender']);
        Route::patch('/interacciones/{id}/estado', [CuentaInteraccionController::class, 'cambiarEstado']);
    });
});
