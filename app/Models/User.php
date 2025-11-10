<?php

namespace App\Models;

/**
 * Alias de Usuario para compatibilidad con paquetes de Laravel
 * 
 * Laravel y muchos paquetes (como Sanctum) esperan que el modelo
 * de usuario esté en App\Models\User. Este alias mantiene la
 * compatibilidad mientras seguimos la arquitectura DDD con el
 * modelo real en App\Domain\Core\Models\Usuario.
 * 
 * Este archivo NO contiene lógica - solo hereda de Usuario.
 */
class User extends \App\Domain\Core\Models\Usuario
{
    // Vacío - solo hereda de Usuario
    // Toda la lógica está en App\Domain\Core\Models\Usuario
}
