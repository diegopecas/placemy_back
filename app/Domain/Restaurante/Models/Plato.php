<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class Plato extends Model
{
    protected $table = 'platos';
    
    protected $fillable = [
        'categoria_id',
        'codigo_plato',
        'nombre',
        'descripcion',
        'costo',
        'foto_url',
        'video_url',
        'tiempo_preparacion_min',
        'etiquetas',
    ];
    
    protected $casts = [
        'etiquetas' => 'array',
        'costo' => 'decimal:2',
    ];
    
    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(CategoriaMenu::class, 'categoria_id');
    }
    
    public function alergenos()
    {
        return $this->belongsToMany(
            Alergeno::class,
            'plato_alergenos',
            'plato_id',
            'alergeno_id'
        )->withTimestamps();
    }
    
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'plato_productos',
            'plato_id',
            'producto_id'
        )->withPivot('cantidad', 'es_modificable')
          ->withTimestamps();
    }
    
    public function restaurantes()
    {
        return $this->belongsToMany(
            Restaurante::class,
            'restaurante_platos',
            'plato_id',
            'restaurante_id'
        )->withPivot('precio', 'disponible', 'calificacion_promedio', 'activo')
          ->withTimestamps();
    }
}
