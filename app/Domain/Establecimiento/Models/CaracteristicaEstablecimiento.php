<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class CaracteristicaEstablecimiento extends Model
{
    protected $table = 'caracteristicas_establecimiento';
    
    protected $fillable = [
        'nombre',
        'descripcion',
        'icono',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_caracteristicas',
            'caracteristica_id',
            'establecimiento_id'
        )->withPivot('valor')
          ->withTimestamps();
    }
}
