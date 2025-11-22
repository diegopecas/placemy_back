<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class EstablecimientoProducto extends Model
{
    protected $table = 'establecimiento_productos';
    
    protected $fillable = [
        'establecimiento_id',
        'producto_id',
        'precio_individual',
        'disponible',
        'activo',
    ];
    
    protected $casts = [
        'precio_individual' => 'decimal:2',
        'disponible' => 'boolean',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}
