<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class EstablecimientoPlato extends Model
{
    protected $table = 'establecimiento_platos';
    
    protected $fillable = [
        'establecimiento_id',
        'plato_id',
        'precio',
        'disponible',
        'calificacion_promedio',
        'activo',
    ];
    
    protected $casts = [
        'precio' => 'decimal:2',
        'calificacion_promedio' => 'decimal:2',
        'disponible' => 'boolean',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function plato()
    {
        return $this->belongsTo(Plato::class, 'plato_id');
    }
}
