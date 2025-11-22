<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaEstado extends Model
{
    protected $table = 'cuenta_estados';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'color',
        'icono',
        'orden',
        'activo',
    ];
    
    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuentas()
    {
        return $this->hasMany(Cuenta::class, 'estado_id');
    }
}
