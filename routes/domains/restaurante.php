<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Restaurante\Controllers\RestauranteController;
use App\Domain\Restaurante\Controllers\MesaController;
use App\Domain\Restaurante\Controllers\PlatoController;
use App\Domain\Restaurante\Controllers\ProductoController;
use App\Domain\Restaurante\Controllers\StaffController;

/*
|--------------------------------------------------------------------------
| Rutas del Dominio Restaurante
|--------------------------------------------------------------------------
|
| Todas las rutas están protegidas con middleware auth:sanctum
| Se deben cargar desde routes/api.php
|
*/

Route::prefix('restaurante')->name('restaurante.')->group(function () {
    
    // =====================================================
    // RESTAURANTES
    // =====================================================
    Route::prefix('restaurantes')->name('restaurantes.')->group(function () {
        Route::get('/', [RestauranteController::class, 'index'])->name('index');
        Route::get('/{id}', [RestauranteController::class, 'show'])->name('show');
        Route::get('/slug/{slug}', [RestauranteController::class, 'showBySlug'])->name('showBySlug');
        Route::post('/', [RestauranteController::class, 'store'])->name('store');
        Route::put('/{id}', [RestauranteController::class, 'update'])->name('update');
        Route::patch('/{id}/estado', [RestauranteController::class, 'cambiarEstado'])->name('cambiarEstado');
        Route::patch('/{id}/verificar', [RestauranteController::class, 'verificar'])->name('verificar');
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
        
        // Gestión de platos en restaurantes
        Route::post('/{id}/asignar-restaurante', [PlatoController::class, 'asignarARestaurante'])->name('asignarARestaurante');
        Route::put('/{id}/restaurante/{restauranteId}', [PlatoController::class, 'actualizarEnRestaurante'])->name('actualizarEnRestaurante');
        Route::delete('/{id}/restaurante/{restauranteId}', [PlatoController::class, 'desasignarDeRestaurante'])->name('desasignarDeRestaurante');
    });
    
    // =====================================================
    // PRODUCTOS
    // =====================================================
    Route::prefix('productos')->name('productos.')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('index');
        Route::get('/{id}', [ProductoController::class, 'show'])->name('show');
        Route::post('/', [ProductoController::class, 'store'])->name('store');
        Route::put('/{id}', [ProductoController::class, 'update'])->name('update');
        
        // Gestión de productos en restaurantes
        Route::post('/{id}/asignar-restaurante', [ProductoController::class, 'asignarARestaurante'])->name('asignarARestaurante');
        Route::put('/{id}/restaurante/{restauranteId}', [ProductoController::class, 'actualizarEnRestaurante'])->name('actualizarEnRestaurante');
        Route::delete('/{id}/restaurante/{restauranteId}', [ProductoController::class, 'desasignarDeRestaurante'])->name('desasignarDeRestaurante');
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
        
        // Gestión de staff en restaurantes
        Route::post('/{id}/asignar-restaurante', [StaffController::class, 'asignarARestaurante'])->name('asignarARestaurante');
        Route::put('/{id}/restaurante/{restauranteId}', [StaffController::class, 'actualizarEnRestaurante'])->name('actualizarEnRestaurante');
        Route::delete('/{id}/restaurante/{restauranteId}', [StaffController::class, 'desasignarDeRestaurante'])->name('desasignarDeRestaurante');
    });
    
});

// =====================================================
// CATÁLOGOS (Solo lectura - cualquier usuario autenticado)
// =====================================================
Route::middleware(['auth:sanctum'])->prefix('restaurante')->name('restaurante.')->group(function () {
    
    // Obtener todos los catálogos de una vez
    Route::get('/catalogos', [\App\Domain\Restaurante\Controllers\CatalogoController::class, 'index'])
        ->name('catalogos.index');
    
    // Obtener un catálogo específico
    Route::get('/catalogos/{tipo}', [\App\Domain\Restaurante\Controllers\CatalogoController::class, 'show'])
        ->name('catalogos.show');
    
});
