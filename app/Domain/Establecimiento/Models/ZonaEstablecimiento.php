<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaEstablecimiento extends Model
{
    protected $table = 'zonas_establecimiento';
    
    protected $fillable = [
        'establecimiento_id',
        'nombre',
        'descripcion',
        'capacidad',
        'orden',
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
    
    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'zona_id');
    }
}
