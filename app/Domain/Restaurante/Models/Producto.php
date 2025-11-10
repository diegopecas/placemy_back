<?php

namespace App\Domain\Restaurante\Models;

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
    
    public function restaurantes()
    {
        return $this->belongsToMany(
            Restaurante::class,
            'restaurante_productos',
            'producto_id',
            'restaurante_id'
        )->withPivot('precio_individual', 'disponible', 'activo')
          ->withTimestamps();
    }
}
