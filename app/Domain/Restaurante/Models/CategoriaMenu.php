<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaMenu extends Model
{
    protected $table = 'categorias_menu';
    
    protected $fillable = [
        'restaurante_id',
        'nombre',
        'descripcion',
        'icono',
        'orden',
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
    
    public function platos()
    {
        return $this->hasMany(Plato::class, 'categoria_id');
    }
}
