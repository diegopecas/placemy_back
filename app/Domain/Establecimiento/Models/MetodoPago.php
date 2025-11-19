<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class MetodoPago extends Model
{
    protected $table = 'metodos_pago';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_metodos_pago',
            'metodo_pago_id',
            'establecimiento_id'
        )->withTimestamps();
    }
}
