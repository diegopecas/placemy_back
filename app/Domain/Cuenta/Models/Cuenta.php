<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\Establecimiento;
use App\Domain\Establecimiento\Models\Mesa;
use App\Domain\Establecimiento\Models\EstablecimientoStaff;
use App\Domain\Cliente\Models\Cliente;

class Cuenta extends Model
{
    protected $table = 'cuentas';
    
    protected $fillable = [
        'numero_cuenta',
        'establecimiento_id',
        'mesa_id',
        'establecimiento_staff_id',
        'cerrada_por_staff_id',
        'cliente_id',
        'estado_id',
        'palabra_secreta',
        'incluir_impuestos',
        'subtotal',
        'descuento',
        'total_impuestos',
        'propina',
        'propina_porcentaje',
        'total',
        'fecha_apertura',
        'fecha_cierre',
        'notas_cliente',
        'notas_internas',
        'activo',
    ];
    
    protected $casts = [
        'incluir_impuestos' => 'boolean',
        'subtotal' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total_impuestos' => 'decimal:2',
        'propina' => 'decimal:2',
        'propina_porcentaje' => 'decimal:2',
        'total' => 'decimal:2',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function mesa()
    {
        return $this->belongsTo(Mesa::class, 'mesa_id');
    }
    
    public function mesero()
    {
        return $this->belongsTo(EstablecimientoStaff::class, 'establecimiento_staff_id');
    }
    
    public function cerradoPor()
    {
        return $this->belongsTo(EstablecimientoStaff::class, 'cerrada_por_staff_id');
    }
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function estado()
    {
        return $this->belongsTo(CuentaEstado::class, 'estado_id');
    }
    
    public function items()
    {
        return $this->hasMany(CuentaItem::class, 'cuenta_id');
    }
    
    public function impuestos()
    {
        return $this->hasMany(CuentaImpuesto::class, 'cuenta_id');
    }
    
    public function divisiones()
    {
        return $this->hasMany(CuentaDivision::class, 'cuenta_id');
    }
    
    public function pagos()
    {
        return $this->hasMany(CuentaPago::class, 'cuenta_id');
    }
    
    public function interacciones()
    {
        return $this->hasMany(CuentaInteraccion::class, 'cuenta_id');
    }
}
