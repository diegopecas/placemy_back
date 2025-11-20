<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteCampania extends Model
{
    protected $table = 'cliente_campanias';
    
    protected $fillable = [
        'cliente_establecimiento_id',
        'campania_id',
        'fecha_inscripcion',
        'activo',
    ];
    
    protected $casts = [
        'fecha_inscripcion' => 'date',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function clienteEstablecimiento()
    {
        return $this->belongsTo(ClienteEstablecimiento::class, 'cliente_establecimiento_id');
    }
    
    public function campania()
    {
        return $this->belongsTo(Campania::class, 'campania_id');
    }
    
    public function movimientos()
    {
        return $this->hasMany(ClienteCampaniaMovimiento::class, 'cliente_campania_id');
    }
    
    // =====================================================
    // MÉTODOS DE NEGOCIO
    // =====================================================
    
    /**
     * Calcular saldo de puntos actual
     */
    public function getSaldoPuntosAttribute(): int
    {
        return $this->movimientos()->sum('puntos');
    }
}
