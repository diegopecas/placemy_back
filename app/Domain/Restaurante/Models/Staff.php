<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Core\Models\PersonaNatural;

class Staff extends Model
{
    protected $table = 'staff';
    
    protected $fillable = [
        'persona_id',
        'codigo_empleado',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function persona()
    {
        return $this->belongsTo(PersonaNatural::class, 'persona_id');
    }
    
    public function restaurantes()
    {
        return $this->belongsToMany(
            Restaurante::class,
            'restaurante_staff',
            'staff_id',
            'restaurante_id'
        )->withPivot('cargo_id', 'usuario_id', 'fecha_asignacion', 'activo')
          ->withTimestamps();
    }
    
    public function mesasAsignadas()
    {
        return $this->hasMany(Mesa::class, 'staff_asignado_id');
    }
}
