<?php

namespace App\Domain\Establecimiento\Models;

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
    
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'establecimiento_staff',
            'staff_id',
            'establecimiento_id'
        )->withPivot('cargo_id', 'usuario_id', 'fecha_asignacion', 'activo')
          ->withTimestamps();
    }
    
    public function mesasAsignadas()
    {
        return $this->hasMany(Mesa::class, 'staff_asignado_id');
    }
}
