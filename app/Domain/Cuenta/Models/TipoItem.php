<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class TipoItem extends Model
{
    protected $table = 'tipos_items';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuentaItems()
    {
        return $this->hasMany(CuentaItem::class, 'tipo_item_id');
    }
}
