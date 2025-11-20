<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class CanalContacto extends Model
{
    protected $table = 'canales_contacto';
    
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
    public function clienteCanales()
    {
        return $this->hasMany(ClienteCanalContacto::class, 'canal_contacto_id');
    }
}
