<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteFechaEspecial extends Model
{
    protected $table = 'cliente_fechas_especiales';
    
    protected $fillable = [
        'cliente_id',
        'tipo_fecha_id',
        'fecha',
        'descripcion',
    ];
    
    protected $casts = [
        'fecha' => 'date',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function tipoFecha()
    {
        return $this->belongsTo(TipoFechaEspecial::class, 'tipo_fecha_id');
    }
}
