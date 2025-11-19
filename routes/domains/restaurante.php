<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Establecimiento\Controllers\EstablecimientoController;
use App\Domain\Establecimiento\Controllers\MesaController;
use App\Domain\Establecimiento\Controllers\PlatoController;
use App\Domain\Establecimiento\Controllers\ProductoController;
use App\Domain\Establecimiento\Controllers\StaffController;

/*
|--------------------------------------------------------------------------
| Rutas del Dominio Establecimiento
|--------------------------------------------------------------------------
|
| Todas las rutas están protegidas con middleware auth:sanctum
| Se deben cargar desde routes/api.php
|
*/

Route::prefix('Establecimiento')->name('Establecimiento.')->group(function () {
    
    // =====================================================
    // EstablecimientoS
    // =====================================================
    Route::prefix('Establecimientos')->name('Establecimientos.')->group(function () {
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
        
        // Gestión de platos en Establecimientos
        Route::post('/{id}/asignar-Establecimiento', [PlatoController::class, 'asignarAEstablecimiento'])->name('asignarAEstablecimiento');
        Route::put('/{id}/Establecimiento/{EstablecimientoId}', [PlatoController::class, 'actualizarEnEstablecimiento'])->name('actualizarEnEstablecimiento');
        Route::delete('/{id}/Establecimiento/{EstablecimientoId}', [PlatoController::class, 'desasignarDeEstablecimiento'])->name('desasignarDeEstablecimiento');
    });
    
    // =====================================================
    // PRODUCTOS
    // =====================================================
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('index');
        Route::get('/{id}', [ProductoController::class, 'show'])->name('show');
        Route::post('/', [ProductoController::class, 'store'])->name('store');
        Route::put('/{id}', [ProductoController::class, 'update'])->name('update');
        
        // Gestión de productos en Establecimientos
        Route::post('/{id}/asignar-Establecimiento', [ProductoController::class, 'asignarAEstablecimiento'])->name('asignarAEstablecimiento');
        Route::put('/{id}/Establecimiento/{EstablecimientoId}', [ProductoController::class, 'actualizarEnEstablecimiento'])->name('actualizarEnEstablecimiento');
        Route::delete('/{id}/Establecimiento/{EstablecimientoId}', [ProductoController::class, 'desasignarDeEstablecimiento'])->name('desasignarDeEstablecimiento');
    });
    
    // =====================================================
    // STAFF
    // =====================================================
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/{id}', [StaffController::class, 'show'])->name('show');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::put('/{id}', [StaffController::class, 'update'])->name('update');
        Route::patch('/{id}/estado', [StaffController::class, 'cambiarEstado'])->name('cambiarEstado');
        
        // Gestión de staff en Establecimientos
        Route::post('/{id}/asignar-Establecimiento', [StaffController::class, 'asignarAEstablecimiento'])->name('asignarAEstablecimiento');
        Route::put('/{id}/Establecimiento/{EstablecimientoId}', [StaffController::class, 'actualizarEnEstablecimiento'])->name('actualizarEnEstablecimiento');
        Route::delete('/{id}/Establecimiento/{EstablecimientoId}', [StaffController::class, 'desasignarDeEstablecimiento'])->name('desasignarDeEstablecimiento');
    });
    
});

// =====================================================
// CATÁLOGOS (Solo lectura - cualquier usuario autenticado)
// =====================================================
Route::middleware(['auth:sanctum'])->prefix('Establecimiento')->name('Establecimiento.')->group(function () {
    
    // Obtener todos los catálogos de una vez
    Route::get('/catalogos', [\App\Domain\Establecimiento\Controllers\CatalogoController::class, 'index'])
        ->name('catalogos.index');
    
    // Obtener un catálogo específico
    Route::get('/catalogos/{tipo}', [\App\Domain\Establecimiento\Controllers\CatalogoController::class, 'show'])
        ->name('catalogos.show');
    
});
