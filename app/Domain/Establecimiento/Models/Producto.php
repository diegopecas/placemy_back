<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    
    protected $fillable = [
        'codigo_producto',
        'nombre',
        'descripcion',
        'foto_url',
    ];
    
    // Relaciones
    public function platos()
    {
        return $this->belongsToMany(
            Plato::class,
            'plato_productos',
            'producto_id',
            'plato_id'
        )->withPivot('cantidad', 'es_modificable')
          ->withTimestamps();
    }
    
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_productos',
            'producto_id',
            'establecimiento_id'
        )->withPivot('precio_individual', 'disponible', 'activo')
          ->withTimestamps();
    }
}
