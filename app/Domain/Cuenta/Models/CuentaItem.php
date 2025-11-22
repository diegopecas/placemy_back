<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\EstablecimientoPlato;
use App\Domain\Establecimiento\Models\EstablecimientoProducto;

class CuentaItem extends Model
{
    protected $table = 'cuenta_items';
    
    protected $fillable = [
        'cuenta_id',
        'tipo_item_id',
        'establecimiento_plato_id',
        'establecimiento_producto_id',
        'estado_id',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal',
        'notas_especiales',
    ];
    
    protected $casts = [
        'cantidad' => 'integer',
        'precio_unitario' => 'decimal:2',
        'descuento' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
    
    public function tipoItem()
    {
        return $this->belongsTo(TipoItem::class, 'tipo_item_id');
    }
    
    public function plato()
    {
        return $this->belongsTo(EstablecimientoPlato::class, 'establecimiento_plato_id');
    }
    
    public function producto()
    {
        return $this->belongsTo(EstablecimientoProducto::class, 'establecimiento_producto_id');
    }
    
    public function estado()
    {
        return $this->belongsTo(CuentaItemEstado::class, 'estado_id');
    }
    
    public function divisiones()
    {
        return $this->hasMany(CuentaItemDivision::class, 'cuenta_item_id');
    }
}
