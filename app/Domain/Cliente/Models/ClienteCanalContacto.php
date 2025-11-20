<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;

class ClienteCanalContacto extends Model
{
    protected $table = 'cliente_canales_contacto';
    
    protected $fillable = [
        'cliente_establecimiento_id',
        'canal_contacto_id',
        'valor',
        'es_preferido',
    ];
    
    protected $casts = [
        'es_preferido' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function clienteEstablecimiento()
    {
        return $this->belongsTo(ClienteEstablecimiento::class, 'cliente_establecimiento_id');
    }
    
    public function canalContacto()
    {
        return $this->belongsTo(CanalContacto::class, 'canal_contacto_id');
    }
}
