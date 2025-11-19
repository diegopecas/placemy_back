<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaMenu extends Model
{
    protected $table = 'categorias_menu';
    
    protected $fillable = [
        'establecimiento_id',
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
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function platos()
    {
        return $this->hasMany(Plato::class, 'categoria_id');
    }
}
