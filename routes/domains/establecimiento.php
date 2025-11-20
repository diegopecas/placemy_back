<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Establecimiento\Controllers\EstablecimientoController;
use App\Domain\Establecimiento\Controllers\MesaController;
use App\Domain\Establecimiento\Controllers\PlatoController;
use App\Domain\Establecimiento\Controllers\ProductoController;
use App\Domain\Establecimiento\Controllers\EstablecimientoStaffController;

/*
|--------------------------------------------------------------------------
| Rutas del Dominio Establecimiento
|--------------------------------------------------------------------------
|
| Todas las rutas están protegidas con middleware auth:sanctum
| Se deben cargar desde routes/api.php
|
*/

Route::prefix('establecimiento')->name('establecimiento.')->group(function () {
    
    // =====================================================
    // ESTABLECIMIENTOS
    // =====================================================
    Route::prefix('establecimientos')->name('establecimientos.')->group(function () {
        Route::get('/', [EstablecimientoController::class, 'index'])->name('index');
        Route::get('/{id}', [EstablecimientoController::class, 'show'])->name('show');
        Route::get('/slug/{slug}', [EstablecimientoController::class, 'showBySlug'])->name('showBySlug');
        Route::post('/', [EstablecimientoController::class, 'store'])->name('store');
        Route::put('/{id}', [EstablecimientoController::class, 'update'])->name('update');
        Route::patch('/{id}/estado', [EstablecimientoController::class, 'cambiarEstado'])->name('cambiarEstado');
        Route::patch('/{id}/verificar', [EstablecimientoController::class, 'verificar'])->name('verificar');
    });
    
    // =====================================================
    // MESAS
    // =====================================================
    Route::prefix('mesas')->name('mesas.')->group(function () {
        Route::get('/', [MesaController::class, 'index'])->name('index');
        Route::get('/{id}', [MesaController::class, 'show'])->name('show');
        Route::post('/', [MesaController::class, 'store'])->name('store');
        Route::put('/{id}', [MesaController::class, 'update'])->name('update');
        Route::patch('/{id}/estado', [MesaController::class, 'cambiarEstado'])->name('cambiarEstado');
        Route::patch('/{id}/asignar-staff', [MesaController::class, 'asignarStaff'])->name('asignarStaff');
    });
    
    // =====================================================
    // PLATOS
    // =====================================================
    Route::prefix('platos')->name('platos.')->group(function () {
        Route::get('/', [PlatoController::class, 'index'])->name('index');
        Route::get('/{id}', [PlatoController::class, 'show'])->name('show');
        Route::post('/', [PlatoController::class, 'store'])->name('store');
        Route::put('/{id}', [PlatoController::class, 'update'])->name('update');
        
        // Gestión de platos en establecimientos
        Route::post('/{id}/asignar-establecimiento', [PlatoController::class, 'asignarAEstablecimiento'])->name('asignarAEstablecimiento');
        Route::put('/{id}/establecimiento/{establecimientoId}', [PlatoController::class, 'actualizarEnEstablecimiento'])->name('actualizarEnEstablecimiento');
        Route::delete('/{id}/establecimiento/{establecimientoId}', [PlatoController::class, 'desasignarDeEstablecimiento'])->name('desasignarDeEstablecimiento');
    });
    
    // =====================================================
    // PRODUCTOS
    // =====================================================
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('index');
        Route::get('/{id}', [ProductoController::class, 'show'])->name('show');
        Route::post('/', [ProductoController::class, 'store'])->name('store');
        Route::put('/{id}', [ProductoController::class, 'update'])->name('update');
        
        // Gestión de productos en establecimientos
        Route::post('/{id}/asignar-establecimiento', [ProductoController::class, 'asignarAEstablecimiento'])->name('asignarAEstablecimiento');
        Route::put('/{id}/establecimiento/{establecimientoId}', [ProductoController::class, 'actualizarEnEstablecimiento'])->name('actualizarEnEstablecimiento');
        Route::delete('/{id}/establecimiento/{establecimientoId}', [ProductoController::class, 'desasignarDeEstablecimiento'])->name('desasignarDeEstablecimiento');
    });
    
    // =====================================================
    // STAFF (por establecimiento)
    // =====================================================
    Route::prefix('establecimientos/{establecimientoId}/staff')->name('staff.')->group(function () {
        Route::get('/', [EstablecimientoStaffController::class, 'index'])->name('index');
        Route::get('/cargo/{cargoId}', [EstablecimientoStaffController::class, 'porCargo'])->name('porCargo');
    });
    
    // Staff individual (CRUD)
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/{id}', [EstablecimientoStaffController::class, 'show'])->name('show');
        Route::post('/', [EstablecimientoStaffController::class, 'store'])->name('store');
        Route::put('/{id}', [EstablecimientoStaffController::class, 'update'])->name('update');
        Route::delete('/{id}', [EstablecimientoStaffController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/estado', [EstablecimientoStaffController::class, 'cambiarEstado'])->name('cambiarEstado');
    });
    
});

// =====================================================
// CATÁLOGOS (Solo lectura - cualquier usuario autenticado)
// =====================================================
Route::middleware(['auth:sanctum'])->prefix('establecimiento')->name('establecimiento.')->group(function () {
    
    // Obtener todos los catálogos de una vez
    Route::get('/catalogos', [\App\Domain\Establecimiento\Controllers\CatalogoController::class, 'index'])
        ->name('catalogos.index');
    
    // Obtener un catálogo específico
    Route::get('/catalogos/{tipo}', [\App\Domain\Establecimiento\Controllers\CatalogoController::class, 'show'])
        ->name('catalogos.show');
    
});