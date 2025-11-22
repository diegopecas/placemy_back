<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class TipoImpuesto extends Model
{
    protected $table = 'tipos_impuestos';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'porcentaje',
        'aplica_sobre',
        'activo',
    ];
    
    protected $casts = [
        'porcentaje' => 'decimal:2',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuentaImpuestos()
    {
        return $this->hasMany(CuentaImpuesto::class, 'tipo_impuesto_id');
    }
}
