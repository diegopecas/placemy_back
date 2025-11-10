<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoMesa extends Model
{
    protected $table = 'estados_mesa';
    
    protected $fillable = [
        'nombre',
        'icono',
        'color',
    ];
    
    // Relaciones
    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'estado_id');
    }
}
