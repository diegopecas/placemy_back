<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $table = 'mesas';
    
    protected $fillable = [
        'restaurante_id',
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
    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'restaurante_id');
    }
    
    public function zona()
    {
        return $this->belongsTo(ZonaRestaurante::class, 'zona_id');
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
