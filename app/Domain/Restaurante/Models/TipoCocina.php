<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCocina extends Model
{
    protected $table = 'tipos_cocina';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function restaurantes()
    {
        return $this->hasMany(Restaurante::class, 'tipo_cocina_id');
    }
}
