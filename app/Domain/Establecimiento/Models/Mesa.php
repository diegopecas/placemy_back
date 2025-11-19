<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $table = 'mesas';
    
    protected $fillable = [
        'establecimiento_id',
        'zona_id',
        'identificacion_mesa',
        'capacidad',
        'estado_id',
        'staff_asignado_id',
        'forma',
        'posicion_x',
        'posicion_y',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function zona()
    {
        return $this->belongsTo(ZonaEstablecimiento::class, 'zona_id');
    }
    
    public function estado()
    {
        return $this->belongsTo(EstadoMesa::class, 'estado_id');
    }
    
    public function staffAsignado()
    {
        return $this->belongsTo(Staff::class, 'staff_asignado_id');
    }
}
