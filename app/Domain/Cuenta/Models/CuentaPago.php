<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\MetodoPago;

class CuentaPago extends Model
{
    protected $table = 'cuenta_pagos';
    
    protected $fillable = [
        'cuenta_id',
        'cuenta_division_id',
        'metodo_pago_id',
        'monto',
        'fecha_pago',
        'referencia',
        'url_soporte',
        'notas',
    ];
    
    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'datetime',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
    
    public function division()
    {
        return $this->belongsTo(CuentaDivision::class, 'cuenta_division_id');
    }
    
    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'metodo_pago_id');
    }
}
