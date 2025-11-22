<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaInteraccion extends Model
{
    protected $table = 'categorias_interacciones';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'icono',
        'orden',
        'activo',
    ];
    
    protected $casts = [
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function tiposInteracciones()
    {
        return $this->hasMany(TipoInteraccion::class, 'categoria_interaccion_id');
    }
}
