<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Cuenta\Controllers\CuentaController;
use App\Domain\Cuenta\Controllers\CuentaItemController;
use App\Domain\Cuenta\Controllers\CuentaInteraccionController;
use App\Domain\Cuenta\Controllers\CatalogoController;

/*
|--------------------------------------------------------------------------
| Rutas del Dominio Cuenta
|--------------------------------------------------------------------------
|
| Los catálogos son públicos (sin autenticación)
| Las rutas de gestión están protegidas con middleware auth:sanctum
| Los permisos se validan por establecimiento usando el middleware 'permission'
|
| IMPORTANTE: El establecimiento_id debe enviarse en:
| - Query params: ?establecimiento_id=1
| - Body: { "establecimiento_id": 1 }
| - Route params: /establecimiento/{establecimientoId}/...
|
*/

Route::prefix('cuentas')->name('cuentas.')->group(function () {
    
    // =====================================================
    // CATÁLOGOS (Públicos - Sin autenticación)
    // =====================================================
    
    // ✅ NUEVO: Todos los catálogos en una sola petición
    Route::get('/catalogos', [CatalogoController::class, 'index'])
        ->name('catalogos.index');
    
    // Estados de cuenta
    Route::get('/catalogos/estados', [CatalogoController::class, 'estadosCuenta'])
        ->name('catalogos.estados');
    
    // Estados de items de cuenta
    Route::get('/catalogos/estados-item', [CatalogoController::class, 'estadosCuentaItem'])
        ->name('catalogos.estados_item');
    
    // Tipos de impuestos
    Route::get('/catalogos/tipos-impuestos', [CatalogoController::class, 'tiposImpuestos'])
        ->name('catalogos.tipos_impuestos');
    
    // Tipos de items (plato, producto)
    Route::get('/catalogos/tipos-items', [CatalogoController::class, 'tiposItems'])
        ->name('catalogos.tipos_items');
    
    // Categorías de interacciones
    Route::get('/catalogos/categorias-interacciones', [CatalogoController::class, 'categoriasInteracciones'])
        ->name('catalogos.categorias_interacciones');
    
    // Tipos de interacciones (opcionalmente filtrado por categoría)
    Route::get('/catalogos/tipos-interacciones/{categoriaId?}', [CatalogoController::class, 'tiposInteracciones'])
        ->name('catalogos.tipos_interacciones');
    
    // Estados de interacciones
    Route::get('/catalogos/estados-interacciones', [CatalogoController::class, 'estadosInteracciones'])
        ->name('catalogos.estados_interacciones');
    
    // =====================================================
    // RUTAS PROTEGIDAS (Requieren autenticación)
    // =====================================================
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // =====================================================
        // CUENTAS
        // =====================================================
        
        // Listar cuentas (requiere establecimiento_id en query)
        Route::get('/', [CuentaController::class, 'index'])
            ->middleware('permission:cuentas.ver')
            ->name('index');
        
        // Ver detalle de cuenta
        Route::get('/{id}', [CuentaController::class, 'show'])
            ->middleware('permission:cuentas.ver')
            ->name('show');
        
        // Buscar cuenta por número
        Route::get('/numero/{numeroCuenta}', [CuentaController::class, 'showByNumero'])
            ->middleware('permission:cuentas.ver')
            ->name('showByNumero');
        
        // Buscar cuenta por palabra secreta (para acceso del cliente)
        Route::get('/palabra/{palabraSecreta}', [CuentaController::class, 'showByPalabraSecreta'])
            ->middleware('permission:cuentas.ver')
            ->name('showByPalabraSecreta');
        
        // Obtener cuenta activa de una mesa
        Route::get('/mesa/{mesaId}/activa', [CuentaController::class, 'showActivaMesa'])
            ->middleware('permission:cuentas.ver')
            ->name('showActivaMesa');
        
        // Crear cuenta
        Route::post('/', [CuentaController::class, 'store'])
            ->middleware('permission:cuentas.crear')
            ->name('store');
        
        // Actualizar cuenta
        Route::put('/{id}', [CuentaController::class, 'update'])
            ->middleware('permission:cuentas.editar')
            ->name('update');
        
        // Cambiar estado de cuenta
        Route::patch('/{id}/estado', [CuentaController::class, 'cambiarEstado'])
            ->middleware('permission:cuentas.editar')
            ->name('cambiarEstado');
        
        // Cerrar cuenta
        Route::patch('/{id}/cerrar', [CuentaController::class, 'cerrar'])
            ->middleware('permission:cuentas.cerrar')
            ->name('cerrar');
        
        // Cancelar cuenta
        Route::patch('/{id}/cancelar', [CuentaController::class, 'cancelar'])
            ->middleware('permission:cuentas.cancelar')
            ->name('cancelar');
        
        // Recalcular totales de la cuenta
        Route::post('/{id}/calcular-totales', [CuentaController::class, 'calcularTotales'])
            ->middleware('permission:cuentas.editar')
            ->name('calcularTotales');
        
        // =====================================================
        // ITEMS DE CUENTA (Consolidado en cuentas.*)
        // =====================================================
        
        // Listar items de una cuenta
        Route::get('/{cuentaId}/items', [CuentaItemController::class, 'index'])
            ->middleware('permission:cuentas.ver')
            ->name('items.index');
        
        // Listar items modificables de una cuenta
        Route::get('/{cuentaId}/items/modificables', [CuentaItemController::class, 'modificables'])
            ->middleware('permission:cuentas.ver')
            ->name('items.modificables');
        
        // Ver detalle de item
        Route::get('/items/{id}', [CuentaItemController::class, 'show'])
            ->middleware('permission:cuentas.ver')
            ->name('items.show');
        
        // Agregar item a cuenta
        Route::post('/items', [CuentaItemController::class, 'store'])
            ->middleware('permission:cuentas.crear')
            ->name('items.store');
        
        // Actualizar item
        Route::put('/items/{id}', [CuentaItemController::class, 'update'])
            ->middleware('permission:cuentas.editar')
            ->name('items.update');
        
        // Eliminar item
        Route::delete('/items/{id}', [CuentaItemController::class, 'destroy'])
            ->middleware('permission:cuentas.editar')
            ->name('items.destroy');
        
        // Cambiar estado de item
        Route::patch('/items/{id}/estado', [CuentaItemController::class, 'cambiarEstado'])
            ->middleware('permission:cuentas.editar')
            ->name('items.cambiarEstado');
        
        // =====================================================
        // INTERACCIONES DEL CLIENTE (Consolidado en cuentas.*)
        // =====================================================
        
        // Listar interacciones de una cuenta
        Route::get('/{cuentaId}/interacciones', [CuentaInteraccionController::class, 'index'])
            ->middleware('permission:cuentas.ver')
            ->name('interacciones.index');
        
        // Listar interacciones pendientes de un establecimiento (establecimientoId en URL)
        Route::get('/interacciones/pendientes/establecimiento/{establecimientoId}', [CuentaInteraccionController::class, 'pendientes'])
            ->middleware('permission:cuentas.ver')
            ->name('interacciones.pendientes');
        
        // Ver detalle de interacción
        Route::get('/interacciones/{id}', [CuentaInteraccionController::class, 'show'])
            ->middleware('permission:cuentas.ver')
            ->name('interacciones.show');
        
        // Crear interacción (cliente llama al mesero, pide algo, envía termómetro emocional, etc.)
        Route::post('/interacciones', [CuentaInteraccionController::class, 'store'])
            ->middleware('permission:cuentas.crear')
            ->name('interacciones.store');
        
        // Atender interacción (el staff marca como atendida)
        Route::patch('/interacciones/{id}/atender', [CuentaInteraccionController::class, 'atender'])
            ->middleware('permission:cuentas.editar')
            ->name('interacciones.atender');
        
        // Cambiar estado de interacción
        Route::patch('/interacciones/{id}/estado', [CuentaInteraccionController::class, 'cambiarEstado'])
            ->middleware('permission:cuentas.editar')
            ->name('interacciones.cambiarEstado');
    });
});