<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Core\Models\Usuario;

class EstablecimientoStaff extends Model
{
    protected $table = 'establecimiento_staff';
    
    protected $fillable = [
        'establecimiento_id',
        'cargo_id',
        'usuario_id',
        'codigo_empleado',
        'fecha_asignacion',
        'activo',
    ];
    
    protected $casts = [
        'fecha_asignacion' => 'date',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }
    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
    
    public function mesasAsignadas()
    {
        return $this->hasMany(Mesa::class, 'establecimiento_staff_id');
    }
}
