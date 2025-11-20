<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCampania extends Model
{
    protected $table = 'tipos_campania';
    
    protected $fillable = [
        'nombre',
        'codigo',
        'descripcion',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function campanias()
    {
        return $this->hasMany(Campania::class, 'tipo_campania_id');
    }
}
