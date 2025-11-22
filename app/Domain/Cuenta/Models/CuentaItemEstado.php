<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaItemEstado extends Model
{
    protected $table = 'cuenta_item_estados';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'color',
        'icono',
        'permite_modificacion',
        'orden',
        'activo',
    ];
    
    protected $casts = [
        'permite_modificacion' => 'boolean',
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuentaItems()
    {
        return $this->hasMany(CuentaItem::class, 'estado_id');
    }
}
