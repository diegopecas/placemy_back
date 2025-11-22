<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaImpuesto extends Model
{
    protected $table = 'cuenta_impuestos';
    
    protected $fillable = [
        'cuenta_id',
        'tipo_impuesto_id',
        'base_gravable',
        'porcentaje_aplicado',
        'monto',
    ];
    
    protected $casts = [
        'base_gravable' => 'decimal:2',
        'porcentaje_aplicado' => 'decimal:2',
        'monto' => 'decimal:2',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
    
    public function tipoImpuesto()
    {
        return $this->belongsTo(TipoImpuesto::class, 'tipo_impuesto_id');
    }
}
