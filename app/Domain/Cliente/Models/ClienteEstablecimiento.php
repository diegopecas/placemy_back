<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\Establecimiento;
use App\Domain\Establecimiento\Models\ZonaEstablecimiento;

class ClienteEstablecimiento extends Model
{
    protected $table = 'cliente_establecimiento';
    
    protected $fillable = [
        'cliente_id',
        'establecimiento_id',
        'zona_preferida_id',
        'notas_internas',
        'acepta_promociones',
        'fecha_primera_visita',
        'calificacion_interna',
        'motivo_calificacion',
    ];
    
    protected $casts = [
        'acepta_promociones' => 'boolean',
        'fecha_primera_visita' => 'date',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function zonaPreferida()
    {
        return $this->belongsTo(ZonaEstablecimiento::class, 'zona_preferida_id');
    }
    
    public function canalesContacto()
    {
        return $this->hasMany(ClienteCanalContacto::class, 'cliente_establecimiento_id');
    }
    
    public function campanias()
    {
        return $this->hasMany(ClienteCampania::class, 'cliente_establecimiento_id');
    }
    
    public function resenas()
    {
        return $this->hasMany(Resena::class, 'cliente_establecimiento_id');
    }
}
