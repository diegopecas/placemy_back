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
| Se deben cargar desde routes/api.php
|
*/

Route::prefix('cliente')->name('cliente.')->group(function () {
    
    // =====================================================
    // CLIENTES
    // =====================================================
    Route::prefix('clientes')->name('clientes.')->group(function () {
        Route::get('/', [ClienteController::class, 'index'])->name('index');
        Route::get('/{id}', [ClienteController::class, 'show'])->name('show');
        Route::post('/', [ClienteController::class, 'store'])->name('store');
        Route::post('/completo', [ClienteController::class, 'storeCompleto'])->name('storeCompleto');
        Route::put('/{id}', [ClienteController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClienteController::class, 'destroy'])->name('destroy');
    });
    
    // =====================================================
    // CLIENTE-ESTABLECIMIENTO (Asociaciones)
    // =====================================================
    
    // Por cliente
    Route::get('/clientes/{clienteId}/establecimientos', [ClienteEstablecimientoController::class, 'indexPorCliente'])
        ->name('clientes.establecimientos.index');
    
    // Por establecimiento
    Route::get('/establecimientos/{establecimientoId}/clientes', [ClienteEstablecimientoController::class, 'indexPorEstablecimiento'])
        ->name('establecimientos.clientes.index');
    
    // CRUD de asociaciones
    Route::prefix('cliente-establecimiento')->name('cliente_establecimiento.')->group(function () {
        Route::get('/{id}', [ClienteEstablecimientoController::class, 'show'])->name('show');
        Route::post('/', [ClienteEstablecimientoController::class, 'store'])->name('store');
        Route::put('/{id}', [ClienteEstablecimientoController::class, 'update'])->name('update');
        Route::delete('/{id}', [ClienteEstablecimientoController::class, 'destroy'])->name('destroy');
    });
    
    // =====================================================
    // CAMPAÑAS
    // =====================================================
    
    // Por establecimiento
    Route::get('/establecimientos/{establecimientoId}/campanias', [CampaniaController::class, 'index'])
        ->name('establecimientos.campanias.index');
    
    // CRUD de campañas
    Route::prefix('campanias')->name('campanias.')->group(function () {
        Route::get('/{id}', [CampaniaController::class, 'show'])->name('show');
        Route::post('/', [CampaniaController::class, 'store'])->name('store');
        Route::put('/{id}', [CampaniaController::class, 'update'])->name('update');
        Route::delete('/{id}', [CampaniaController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/estado', [CampaniaController::class, 'cambiarEstado'])->name('cambiarEstado');
    });
    
    // =====================================================
    // RESEÑAS
    // =====================================================
    
    // Por establecimiento
    Route::get('/establecimientos/{establecimientoId}/resenas', [ResenaController::class, 'indexPorEstablecimiento'])
        ->name('establecimientos.resenas.index');
    
    // Por cliente-establecimiento
    Route::get('/cliente-establecimiento/{clienteEstablecimientoId}/resenas', [ResenaController::class, 'indexPorClienteEstablecimiento'])
        ->name('cliente_establecimiento.resenas.index');
    
    // CRUD de reseñas
    Route::prefix('resenas')->name('resenas.')->group(function () {
        Route::get('/{id}', [ResenaController::class, 'show'])->name('show');
        Route::post('/', [ResenaController::class, 'store'])->name('store');
        Route::put('/{id}', [ResenaController::class, 'update'])->name('update');
        Route::delete('/{id}', [ResenaController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/responder', [ResenaController::class, 'responder'])->name('responder');
    });
    
});

// =====================================================
// CATÁLOGOS (Solo lectura - cualquier usuario autenticado)
// =====================================================
Route::middleware(['auth:sanctum'])->prefix('cliente')->name('cliente.')->group(function () {
    
    // Obtener todos los catálogos de una vez
    Route::get('/catalogos', [CatalogoController::class, 'index'])
        ->name('catalogos.index');
    
    // Obtener un catálogo específico
    Route::get('/catalogos/{tipo}', [CatalogoController::class, 'show'])
        ->name('catalogos.show');
    
});
