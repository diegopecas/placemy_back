<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class RangoPrecio extends Model
{
    protected $table = 'rangos_precio';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function establecimientos()
    {
        return $this->hasMany(Establecimiento::class, 'rango_precio_id');
    }
}
