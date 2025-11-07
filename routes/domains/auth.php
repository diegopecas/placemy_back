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
*/

Route::prefix('auth')->name('auth.')->group(function () {
    
    // Rutas públicas (sin autenticación)
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    
    // Rutas protegidas (requieren autenticación)
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::get('/me', [AuthController::class, 'me'])->name('me');
    });
});
