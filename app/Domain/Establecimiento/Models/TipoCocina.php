<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCocina extends Model
{
    protected $table = 'tipos_cocina';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_tipos_cocina',
            'tipo_cocina_id',
            'establecimiento_id'
        )->withTimestamps();
    }
}
