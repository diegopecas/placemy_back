<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class RangoPrecio extends Model
{
    protected $table = 'rangos_precio';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function restaurantes()
    {
        return $this->hasMany(Restaurante::class, 'rango_precio_id');
    }
}
