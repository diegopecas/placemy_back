<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaDivision extends Model
{
    protected $table = 'cuenta_divisiones';
    
    protected $fillable = [
        'cuenta_id',
        'nombre',
        'subtotal_asignado',
        'impuestos_asignado',
        'propina_asignado',
        'total_asignado',
        'pagado',
    ];
    
    protected $casts = [
        'subtotal_asignado' => 'decimal:2',
        'impuestos_asignado' => 'decimal:2',
        'propina_asignado' => 'decimal:2',
        'total_asignado' => 'decimal:2',
        'pagado' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
    
    public function itemsAsignados()
    {
        return $this->hasMany(CuentaItemDivision::class, 'cuenta_division_id');
    }
    
    public function pagos()
    {
        return $this->hasMany(CuentaPago::class, 'cuenta_division_id');
    }
}
