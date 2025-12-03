<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Auth\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Auth Domain Routes
|--------------------------------------------------------------------------
|
| Rutas de autenticación: login, logout, refresh token, me
|
| NOTA: Las rutas de auth NO requieren validación de establecimiento
| porque son operaciones globales del usuario.
|
*/

Route::prefix('auth')->name('auth.')->group(function () {
    
    // =====================================================
    // RUTAS PÚBLICAS (Sin autenticación)
    // =====================================================
    
    // Login - Autenticación del usuario
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login');
    
    // =====================================================
    // RUTAS PROTEGIDAS (Requieren autenticación)
    // =====================================================
    Route::middleware(['auth:sanctum'])->group(function () {
        
        // Refrescar token de acceso
        Route::post('/refresh', [AuthController::class, 'refresh'])
            ->name('refresh');
        
        // Cerrar sesión (revocar todos los tokens)
        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('logout');
        
        // Obtener información del usuario autenticado
        Route::get('/me', [AuthController::class, 'me'])
            ->name('me');
    });
});