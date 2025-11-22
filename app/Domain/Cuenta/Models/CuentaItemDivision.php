<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaItemDivision extends Model
{
    protected $table = 'cuenta_item_divisiones';
    
    protected $fillable = [
        'cuenta_item_id',
        'cuenta_division_id',
        'porcentaje_asignado',
        'monto_asignado',
    ];
    
    protected $casts = [
        'porcentaje_asignado' => 'decimal:2',
        'monto_asignado' => 'decimal:2',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function item()
    {
        return $this->belongsTo(CuentaItem::class, 'cuenta_item_id');
    }
    
    public function division()
    {
        return $this->belongsTo(CuentaDivision::class, 'cuenta_division_id');
    }
}
