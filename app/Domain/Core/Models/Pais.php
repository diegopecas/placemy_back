<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{
    protected $table = 'core_paises';
    
    protected $fillable = [
        'codigo',
        'nombre',
        'codigo_telefono',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function departamentos()
    {
        return $this->hasMany(Departamento::class, 'pais_id');
    }
}
