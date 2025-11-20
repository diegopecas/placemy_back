<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    protected $table = 'tipos_movimiento';
    
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
    public function movimientos()
    {
        return $this->hasMany(ClienteCampaniaMovimiento::class, 'tipo_movimiento_id');
    }
}
