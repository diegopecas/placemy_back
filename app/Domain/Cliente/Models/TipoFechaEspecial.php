<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class TipoFechaEspecial extends Model
{
    protected $table = 'tipos_fecha_especial';
    
    protected $fillable = [
        'nombre',
        'codigo',
        'icono',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function clienteFechas()
    {
        return $this->hasMany(ClienteFechaEspecial::class, 'tipo_fecha_id');
    }
}
