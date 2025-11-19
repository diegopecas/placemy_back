<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEmpresarial extends Model
{
    protected $table = 'grupos_empresariales';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function establecimientos()
    {
        return $this->belongsToMany(
            Establecimiento::class,
            'grupo_empresarial_establecimientos',
            'grupo_empresarial_id',
            'establecimiento_id'
        )->withTimestamps();
    }
}
