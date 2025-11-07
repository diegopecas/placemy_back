<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Dominio Auth (rutas públicas y protegidas)
require __DIR__.'/domains/auth.php';

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Aquí irán las rutas de otros dominios
    
});