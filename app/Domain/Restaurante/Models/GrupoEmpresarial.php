<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoEmpresarial extends Model
{
    protected $table = 'grupos_empresariales';
    
    protected $fillable = [
        'nombre',
    ];
    
    // Relaciones
    public function restaurantes()
    {
        return $this->belongsToMany(
            Restaurante::class,
            'grupo_empresarial_restaurantes',
            'grupo_empresarial_id',
            'restaurante_id'
        )->withTimestamps();
    }
}
