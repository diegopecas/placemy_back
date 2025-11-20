<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Establecimiento\Models\Establecimiento;

class Campania extends Model
{
    protected $table = 'campanias';
    
    protected $fillable = [
        'establecimiento_id',
        'tipo_campania_id',
        'nombre',
        'descripcion',
        'icono',
        'fecha_inicio',
        'fecha_fin',
        'configuracion_json',
        'activo',
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'configuracion_json' => 'array',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    public function tipoCampania()
    {
        return $this->belongsTo(TipoCampania::class, 'tipo_campania_id');
    }
    
    public function clientesCampanias()
    {
        return $this->hasMany(ClienteCampania::class, 'campania_id');
    }
}
