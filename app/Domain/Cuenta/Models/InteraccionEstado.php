<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class InteraccionEstado extends Model
{
    protected $table = 'interaccion_estados';
    
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
    
    public function cuentaInteracciones()
    {
        return $this->hasMany(CuentaInteraccion::class, 'estado_id');
    }
}
