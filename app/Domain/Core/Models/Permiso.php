<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'core_permisos';
    
    public $timestamps = false;
    
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'modulo',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(
            Rol::class,
            'core_roles_permisos',
            'permiso_id',
            'rol_id'
        );
    }
}
