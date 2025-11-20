<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    protected $table = 'resenas';
    
    protected $fillable = [
        'cliente_establecimiento_id',
        'calificacion',
        'comentario',
        'fecha_resena',
        'respuesta_establecimiento',
        'fecha_respuesta',
    ];
    
    protected $casts = [
        'fecha_resena' => 'date',
        'fecha_respuesta' => 'date',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function clienteEstablecimiento()
    {
        return $this->belongsTo(ClienteEstablecimiento::class, 'cliente_establecimiento_id');
    }
}
