<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class CaracteristicaRestaurante extends Model
{
    protected $table = 'caracteristicas_restaurante';
    
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
    public function restaurantes()
    {
        return $this->belongsToMany(
            Restaurante::class,
            'restaurante_caracteristicas',
            'caracteristica_id',
            'restaurante_id'
        )->withPivot('valor')
          ->withTimestamps();
    }
}
