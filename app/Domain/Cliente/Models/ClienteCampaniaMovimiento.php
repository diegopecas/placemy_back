<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteCampaniaMovimiento extends Model
{
    protected $table = 'cliente_campania_movimientos';
    
    protected $fillable = [
        'cliente_campania_id',
        'tipo_movimiento_id',
        'puntos',
        'cuenta_id',
        'descripcion',
        'fecha',
    ];
    
    protected $casts = [
        'fecha' => 'date',
    ];
    
    // Deshabilitar updated_at (solo created_at)
    const UPDATED_AT = null;
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function clienteCampania()
    {
        return $this->belongsTo(ClienteCampania::class, 'cliente_campania_id');
    }
    
    public function tipoMovimiento()
    {
        return $this->belongsTo(TipoMovimiento::class, 'tipo_movimiento_id');
    }
    
    // Relación con Cuenta (cuando se implemente el dominio)
    // public function cuenta()
    // {
    //     return $this->belongsTo(Cuenta::class, 'cuenta_id');
    // }
}
