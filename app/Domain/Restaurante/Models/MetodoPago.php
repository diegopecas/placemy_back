<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function restaurantes()
    {
        return $this->belongsToMany(
            Restaurante::class,
            'restaurante_metodos_pago',
            'metodo_pago_id',
            'restaurante_id'
        )->withTimestamps();
    }
}
