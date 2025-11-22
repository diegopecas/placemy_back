<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\EstablecimientoStaff;

class CuentaInteraccion extends Model
{
    protected $table = 'cuenta_interacciones';
    
    protected $fillable = [
        'cuenta_id',
        'tipo_interaccion_id',
        'estado_id',
        'valor_numerico',
        'mensaje',
        'opcion_seleccionada',
        'foto_url',
        'fecha_interaccion',
        'fecha_atencion',
        'atendido_por_staff_id',
        'notas_atencion',
    ];
    
    protected $casts = [
        'valor_numerico' => 'integer',
        'fecha_interaccion' => 'datetime',
        'fecha_atencion' => 'datetime',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cuenta()
    {
        return $this->belongsTo(Cuenta::class, 'cuenta_id');
    }
    
    public function tipoInteraccion()
    {
        return $this->belongsTo(TipoInteraccion::class, 'tipo_interaccion_id');
    }
    
    public function estado()
    {
        return $this->belongsTo(InteraccionEstado::class, 'estado_id');
    }
    
    public function atendidoPor()
    {
        return $this->belongsTo(EstablecimientoStaff::class, 'atendido_por_staff_id');
    }
}
