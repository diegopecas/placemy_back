<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class ZonaRestaurante extends Model
{
    protected $table = 'zonas_restaurante';
    
    protected $fillable = [
        'restaurante_id',
        'nombre',
        'descripcion',
        'capacidad',
        'orden',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function restaurante()
    {
        return $this->belongsTo(Restaurante::class, 'restaurante_id');
    }
    
    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'zona_id');
    }
}
