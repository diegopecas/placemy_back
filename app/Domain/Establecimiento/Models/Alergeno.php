<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class Alergeno extends Model
{
    protected $table = 'alergenos';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function platos()
    {
        return $this->belongsToMany(
            Plato::class,
            'plato_alergenos',
            'alergeno_id',
            'plato_id'
        )->withTimestamps();
    }
}
