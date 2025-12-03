<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Establecimiento\Controllers\EstablecimientoController;
use App\Domain\Establecimiento\Controllers\MesaController;
use App\Domain\Establecimiento\Controllers\PlatoController;
use App\Domain\Establecimiento\Controllers\ProductoController;
use App\Domain\Establecimiento\Controllers\EstablecimientoStaffController;
use App\Domain\Establecimiento\Controllers\CatalogoController;

/*
|--------------------------------------------------------------------------
| Rutas del Dominio Establecimiento
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
Route::middleware(['auth:sanctum'])->prefix('establecimiento')->name('establecimiento.')->group(function () {

    // =====================================================
    // ESTABLECIMIENTOS
    // =====================================================
    Route::prefix('establecimientos')->name('establecimientos.')->group(function () {
        
        // Listar establecimientos del usuario (sin permiso específico)
        Route::get('/', [EstablecimientoController::class, 'index'])
            ->name('index');
        
        // Ver detalle de establecimiento
        Route::get('/{id}', [EstablecimientoController::class, 'show'])
            ->middleware('permission:establecimientos.ver')
            ->name('show');
        
        Route::get('/slug/{slug}', [EstablecimientoController::class, 'showBySlug'])
            ->middleware('permission:establecimientos.ver')
            ->name('showBySlug');
        
        // Crear establecimiento
        Route::post('/', [EstablecimientoController::class, 'store'])
            ->middleware('permission:establecimientos.crear')
            ->name('store');
        
        // Actualizar establecimiento
        Route::put('/{id}', [EstablecimientoController::class, 'update'])
            ->middleware('permission:establecimientos.editar')
            ->name('update');
        
        // Cambiar estado del establecimiento
        Route::patch('/{id}/estado', [EstablecimientoController::class, 'cambiarEstado'])
            ->middleware('permission:establecimientos.editar')
            ->name('cambiarEstado');
        
        // Verificar establecimiento
        Route::patch('/{id}/verificar', [EstablecimientoController::class, 'verificar'])
            ->middleware('permission:establecimientos.verificar')
            ->name('verificar');
        
        // Obtener menú del establecimiento
        Route::get('/{id}/menu', [EstablecimientoController::class, 'obtenerMenu'])
            ->middleware('permission:categorias_menu.ver')
            ->name('obtenerMenu');
    });

    // =====================================================
    // MESAS
    // =====================================================
    Route::prefix('mesas')->name('mesas.')->group(function () {
        
        // Listar mesas
        Route::get('/', [MesaController::class, 'index'])
            ->middleware('permission:mesas.ver')
            ->name('index');
        
        // Ver detalle de mesa
        Route::get('/{id}', [MesaController::class, 'show'])
            ->middleware('permission:mesas.ver')
            ->name('show');
        
        // Crear mesa
        Route::post('/', [MesaController::class, 'store'])
            ->middleware('permission:mesas.crear')
            ->name('store');
        
        // Actualizar mesa
        Route::put('/{id}', [MesaController::class, 'update'])
            ->middleware('permission:mesas.editar')
            ->name('update');
        
        // Cambiar estado de mesa
        Route::patch('/{id}/estado', [MesaController::class, 'cambiarEstado'])
            ->middleware('permission:mesas.editar')
            ->name('cambiarEstado');
        
        // Asignar staff a mesa
        Route::patch('/{id}/asignar-staff', [MesaController::class, 'asignarStaff'])
            ->middleware('permission:mesas.editar')
            ->name('asignarStaff');
    });

    // =====================================================
    // ZONAS
    // =====================================================
    Route::prefix('zonas')->name('zonas.')->group(function () {
        
        // Listar zonas
        Route::get('/', [ZonaController::class, 'index'])
            ->middleware('permission:zonas.ver')
            ->name('index');
        
        // Crear zona
        Route::post('/', [ZonaController::class, 'store'])
            ->middleware('permission:zonas.crear')
            ->name('store');
        
        // Actualizar zona
        Route::put('/{id}', [ZonaController::class, 'update'])
            ->middleware('permission:zonas.editar')
            ->name('update');
        
        // Eliminar zona
        Route::delete('/{id}', [ZonaController::class, 'destroy'])
            ->middleware('permission:zonas.eliminar')
            ->name('destroy');
    });

    // =====================================================
    // CATEGORÍAS DE MENÚ
    // =====================================================
    Route::prefix('categorias-menu')->name('categorias_menu.')->group(function () {
        
        // Listar categorías
        Route::get('/', [CategoriaMenuController::class, 'index'])
            ->middleware('permission:categorias_menu.ver')
            ->name('index');
        
        // Crear categoría
        Route::post('/', [CategoriaMenuController::class, 'store'])
            ->middleware('permission:categorias_menu.crear')
            ->name('store');
        
        // Actualizar categoría
        Route::put('/{id}', [CategoriaMenuController::class, 'update'])
            ->middleware('permission:categorias_menu.editar')
            ->name('update');
        
        // Eliminar categoría
        Route::delete('/{id}', [CategoriaMenuController::class, 'destroy'])
            ->middleware('permission:categorias_menu.eliminar')
            ->name('destroy');
    });

    // =====================================================
    // PLATOS (Incluye asignación a establecimiento, alergenos, productos)
    // =====================================================
    Route::prefix('platos')->name('platos.')->group(function () {
        
        // Listar platos
        Route::get('/', [PlatoController::class, 'index'])
            ->middleware('permission:platos.ver')
            ->name('index');
        
        // Ver detalle de plato
        Route::get('/{id}', [PlatoController::class, 'show'])
            ->middleware('permission:platos.ver')
            ->name('show');
        
        // Crear plato y asignarlo a establecimiento
        Route::post('/', [PlatoController::class, 'store'])
            ->middleware('permission:platos.crear')
            ->name('store');
        
        // Actualizar plato (incluye precio, disponibilidad, alergenos)
        Route::put('/{id}', [PlatoController::class, 'update'])
            ->middleware('permission:platos.editar')
            ->name('update');
        
        // Eliminar plato
        Route::delete('/{id}', [PlatoController::class, 'destroy'])
            ->middleware('permission:platos.eliminar')
            ->name('destroy');
        
        // Asignar plato a establecimiento
        Route::post('/{id}/asignar-establecimiento', [PlatoController::class, 'asignarAEstablecimiento'])
            ->middleware('permission:platos.crear')
            ->name('asignarAEstablecimiento');
        
        // Actualizar plato en establecimiento (precio, disponibilidad)
        Route::put('/{id}/establecimiento/{establecimientoId}', [PlatoController::class, 'actualizarEnEstablecimiento'])
            ->middleware('permission:platos.editar')
            ->name('actualizarEnEstablecimiento');
        
        // Desasignar plato de establecimiento
        Route::delete('/{id}/establecimiento/{establecimientoId}', [PlatoController::class, 'desasignarDeEstablecimiento'])
            ->middleware('permission:platos.eliminar')
            ->name('desasignarDeEstablecimiento');
    });

    // =====================================================
    // PRODUCTOS (Incluye asignación a establecimiento)
    // =====================================================
    Route::prefix('productos')->name('productos.')->group(function () {
        
        // Listar productos
        Route::get('/', [ProductoController::class, 'index'])
            ->middleware('permission:productos.ver')
            ->name('index');
        
        // Ver detalle de producto
        Route::get('/{id}', [ProductoController::class, 'show'])
            ->middleware('permission:productos.ver')
            ->name('show');
        
        // Crear producto y asignarlo a establecimiento
        Route::post('/', [ProductoController::class, 'store'])
            ->middleware('permission:productos.crear')
            ->name('store');
        
        // Actualizar producto (incluye precio, disponibilidad)
        Route::put('/{id}', [ProductoController::class, 'update'])
            ->middleware('permission:productos.editar')
            ->name('update');
        
        // Eliminar producto
        Route::delete('/{id}', [ProductoController::class, 'destroy'])
            ->middleware('permission:productos.eliminar')
            ->name('destroy');
        
        // Asignar producto a establecimiento
        Route::post('/{id}/asignar-establecimiento', [ProductoController::class, 'asignarAEstablecimiento'])
            ->middleware('permission:productos.crear')
            ->name('asignarAEstablecimiento');
        
        // Actualizar producto en establecimiento (precio, disponibilidad)
        Route::put('/{id}/establecimiento/{establecimientoId}', [ProductoController::class, 'actualizarEnEstablecimiento'])
            ->middleware('permission:productos.editar')
            ->name('actualizarEnEstablecimiento');
        
        // Desasignar producto de establecimiento
        Route::delete('/{id}/establecimiento/{establecimientoId}', [ProductoController::class, 'desasignarDeEstablecimiento'])
            ->middleware('permission:productos.eliminar')
            ->name('desasignarDeEstablecimiento');
    });

    // =====================================================
    // PERSONAL / STAFF (por establecimiento)
    // =====================================================
    Route::prefix('establecimientos/{establecimientoId}/personal')->name('personal.')->group(function () {
        
        // Listar personal del establecimiento
        Route::get('/', [EstablecimientoStaffController::class, 'index'])
            ->middleware('permission:personal.ver')
            ->name('index');
        
        // Listar personal por cargo
        Route::get('/cargo/{cargoId}', [EstablecimientoStaffController::class, 'porCargo'])
            ->middleware('permission:personal.ver')
            ->name('porCargo');
    });

    // Personal individual (CRUD)
    Route::prefix('personal')->name('personal.')->group(function () {
        
        // Ver detalle de personal
        Route::get('/{id}', [EstablecimientoStaffController::class, 'show'])
            ->middleware('permission:personal.ver')
            ->name('show');
        
        // Crear personal
        Route::post('/', [EstablecimientoStaffController::class, 'store'])
            ->middleware('permission:personal.crear')
            ->name('store');
        
        // Actualizar personal
        Route::put('/{id}', [EstablecimientoStaffController::class, 'update'])
            ->middleware('permission:personal.editar')
            ->name('update');
        
        // Eliminar personal
        Route::delete('/{id}', [EstablecimientoStaffController::class, 'destroy'])
            ->middleware('permission:personal.eliminar')
            ->name('destroy');
        
        // Cambiar estado de personal
        Route::patch('/{id}/estado', [EstablecimientoStaffController::class, 'cambiarEstado'])
            ->middleware('permission:personal.editar')
            ->name('cambiarEstado');
    });

    // =====================================================
    // CARGOS
    // =====================================================
    Route::prefix('cargos')->name('cargos.')->group(function () {
        
        // Listar cargos
        Route::get('/', [CargoController::class, 'index'])
            ->middleware('permission:cargos.ver')
            ->name('index');
        
        // Crear cargo
        Route::post('/', [CargoController::class, 'store'])
            ->middleware('permission:cargos.crear')
            ->name('store');
        
        // Actualizar cargo
        Route::put('/{id}', [CargoController::class, 'update'])
            ->middleware('permission:cargos.editar')
            ->name('update');
        
        // Eliminar cargo
        Route::delete('/{id}', [CargoController::class, 'destroy'])
            ->middleware('permission:cargos.eliminar')
            ->name('destroy');
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