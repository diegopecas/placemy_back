<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'core_ciudades';
    
    protected $fillable = [
        'departamento_id',
        'codigo',
        'nombre',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
    
    public function personasNacimiento()
    {
        return $this->hasMany(PersonaNatural::class, 'ciudad_nacimiento_id');
    }
    
    public function personasResidencia()
    {
        return $this->hasMany(PersonaNatural::class, 'ciudad_residencia_id');
    }
}
