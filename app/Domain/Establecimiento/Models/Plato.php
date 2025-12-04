<?php

namespace App\Domain\Establecimiento\Models;

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
        );  // ✅ CORREGIDO: Sin withTimestamps() porque la tabla pivot no tiene created_at/updated_at
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
    
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_platos',
            'plato_id',
            'establecimiento_id'
        )->withPivot('precio', 'disponible', 'calificacion_promedio', 'activo')
          ->withTimestamps();
    }
}