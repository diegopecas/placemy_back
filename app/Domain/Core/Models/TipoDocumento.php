<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    protected $table = 'core_tipos_documento';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function personasNaturales()
    {
        return $this->hasMany(PersonaNatural::class, 'tipo_documento_id');
    }
}
