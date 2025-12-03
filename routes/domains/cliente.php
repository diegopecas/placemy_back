<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Cliente\Controllers\ClienteController;
use App\Domain\Cliente\Controllers\ClienteEstablecimientoController;
use App\Domain\Cliente\Controllers\CampaniaController;
use App\Domain\Cliente\Controllers\ResenaController;
use App\Domain\Cliente\Controllers\CatalogoController;

/*
|--------------------------------------------------------------------------
| Rutas del Dominio Cliente
|--------------------------------------------------------------------------
|
| Todas las rutas están protegidas con middleware auth:sanctum
| Los permisos se validan por establecimiento usando el middleware 'permission'
|
| IMPORTANTE: El establecimiento_id debe enviarse en:
| - Query params: ?establecimiento_id=1
| - Body: { "establecimiento_id": 1 }
| - Route params: /establecimientos/{establecimientoId}/...
|
*/

// =====================================================
// RUTAS PROTEGIDAS CON AUTENTICACIÓN
// =====================================================
Route::middleware(['auth:sanctum'])->prefix('cliente')->name('cliente.')->group(function () {
    
    // =====================================================
    // CLIENTES
    // =====================================================
    Route::prefix('clientes')->name('clientes.')->group(function () {
        
        // Listar clientes (requiere establecimiento_id)
        Route::get('/', [ClienteController::class, 'index'])
            ->middleware('permission:clientes.ver')
            ->name('index');
        
        // Ver detalle de cliente
        Route::get('/{id}', [ClienteController::class, 'show'])
            ->middleware('permission:clientes.ver')
            ->name('show');
        
        // Crear cliente básico
        Route::post('/', [ClienteController::class, 'store'])
            ->middleware('permission:clientes.crear')
            ->name('store');
        
        // Crear cliente completo (con todos los datos)
        Route::post('/completo', [ClienteController::class, 'storeCompleto'])
            ->middleware('permission:clientes.crear')
            ->name('storeCompleto');
        
        // Actualizar cliente
        Route::put('/{id}', [ClienteController::class, 'update'])
            ->middleware('permission:clientes.editar')
            ->name('update');
        
        // Eliminar cliente
        Route::delete('/{id}', [ClienteController::class, 'destroy'])
            ->middleware('permission:clientes.eliminar')
            ->name('destroy');
    });
    
    // =====================================================
    // CLIENTE-ESTABLECIMIENTO (Asociaciones)
    // =====================================================
    
    // Listar establecimientos de un cliente
    Route::get('/clientes/{clienteId}/establecimientos', [ClienteEstablecimientoController::class, 'indexPorCliente'])
        ->middleware('permission:clientes.ver')
        ->name('clientes.establecimientos.index');
    
    // Listar clientes de un establecimiento (establecimientoId en URL)
    Route::get('/establecimientos/{establecimientoId}/clientes', [ClienteEstablecimientoController::class, 'indexPorEstablecimiento'])
        ->middleware('permission:clientes.ver')
        ->name('establecimientos.clientes.index');
    
    // CRUD de asociaciones cliente-establecimiento
    Route::prefix('cliente-establecimiento')->name('cliente_establecimiento.')->group(function () {
        
        // Ver detalle de asociación
        Route::get('/{id}', [ClienteEstablecimientoController::class, 'show'])
            ->middleware('permission:clientes.ver')
            ->name('show');
        
        // Crear asociación
        Route::post('/', [ClienteEstablecimientoController::class, 'store'])
            ->middleware('permission:clientes.crear')
            ->name('store');
        
        // Actualizar asociación
        Route::put('/{id}', [ClienteEstablecimientoController::class, 'update'])
            ->middleware('permission:clientes.editar')
            ->name('update');
        
        // Eliminar asociación
        Route::delete('/{id}', [ClienteEstablecimientoController::class, 'destroy'])
            ->middleware('permission:clientes.eliminar')
            ->name('destroy');
    });
    
    // =====================================================
    // CAMPAÑAS
    // =====================================================
    
    // Listar campañas por establecimiento (establecimientoId en URL)
    Route::get('/establecimientos/{establecimientoId}/campanias', [CampaniaController::class, 'index'])
        ->middleware('permission:campanias.ver')
        ->name('establecimientos.campanias.index');
    
    // CRUD de campañas
    Route::prefix('campanias')->name('campanias.')->group(function () {
        
        // Ver detalle de campaña
        Route::get('/{id}', [CampaniaController::class, 'show'])
            ->middleware('permission:campanias.ver')
            ->name('show');
        
        // Crear campaña
        Route::post('/', [CampaniaController::class, 'store'])
            ->middleware('permission:campanias.crear')
            ->name('store');
        
        // Actualizar campaña
        Route::put('/{id}', [CampaniaController::class, 'update'])
            ->middleware('permission:campanias.editar')
            ->name('update');
        
        // Eliminar campaña
        Route::delete('/{id}', [CampaniaController::class, 'destroy'])
            ->middleware('permission:campanias.eliminar')
            ->name('destroy');
        
        // Cambiar estado de campaña
        Route::patch('/{id}/estado', [CampaniaController::class, 'cambiarEstado'])
            ->middleware('permission:campanias.editar')
            ->name('cambiarEstado');
    });
    
    // =====================================================
    // RESEÑAS
    // =====================================================
    
    // Listar reseñas por establecimiento (establecimientoId en URL)
    Route::get('/establecimientos/{establecimientoId}/resenas', [ResenaController::class, 'indexPorEstablecimiento'])
        ->middleware('permission:resenas.ver')
        ->name('establecimientos.resenas.index');
    
    // Listar reseñas por cliente-establecimiento
    Route::get('/cliente-establecimiento/{clienteEstablecimientoId}/resenas', [ResenaController::class, 'indexPorClienteEstablecimiento'])
        ->middleware('permission:resenas.ver')
        ->name('cliente_establecimiento.resenas.index');
    
    // CRUD de reseñas
    Route::prefix('resenas')->name('resenas.')->group(function () {
        
        // Ver detalle de reseña
        Route::get('/{id}', [ResenaController::class, 'show'])
            ->middleware('permission:resenas.ver')
            ->name('show');
        
        // Crear reseña
        Route::post('/', [ResenaController::class, 'store'])
            ->middleware('permission:resenas.crear')
            ->name('store');
        
        // Actualizar reseña
        Route::put('/{id}', [ResenaController::class, 'update'])
            ->middleware('permission:resenas.editar')
            ->name('update');
        
        // Eliminar reseña
        Route::delete('/{id}', [ResenaController::class, 'destroy'])
            ->middleware('permission:resenas.eliminar')
            ->name('destroy');
        
        // Responder reseña (desde el establecimiento)
        Route::post('/{id}/responder', [ResenaController::class, 'responder'])
            ->middleware('permission:resenas.responder')
            ->name('responder');
    });
    
    // =====================================================
    // CATÁLOGOS (Solo lectura - cualquier usuario autenticado)
    // =====================================================
    
    // Obtener todos los catálogos de una vez
    Route::get('/catalogos', [CatalogoController::class, 'index'])
        ->name('catalogos.index');
    
    // Obtener un catálogo específico
    Route::get('/catalogos/{tipo}', [CatalogoController::class, 'show'])
        ->name('catalogos.show');
});