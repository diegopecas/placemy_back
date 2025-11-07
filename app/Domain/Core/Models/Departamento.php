<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'core_departamentos';
    
    protected $fillable = [
        'pais_id',
        'codigo',
        'nombre',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_id');
    }
    
    public function ciudades()
    {
        return $this->hasMany(Ciudad::class, 'departamento_id');
    }
}
