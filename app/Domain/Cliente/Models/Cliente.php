<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Core\Models\PersonaNatural;
use App\Domain\Establecimiento\Models\Alergeno;

class Cliente extends Model
{
    protected $table = 'clientes';
    
    protected $fillable = [
        'persona_id',
        'sobrenombre',
        'preferencias_gustos',
        'preferencias_no_gustos',
        'otras_alergias',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function persona()
    {
        return $this->belongsTo(PersonaNatural::class, 'persona_id');
    }
    
    public function establecimientos()
    {
        return $this->hasMany(ClienteEstablecimiento::class, 'cliente_id');
    }
    
    public function alergenos()
    {
        return $this->belongsToMany(
            Alergeno::class,
            'cliente_alergenos',
            'cliente_id',
            'alergeno_id'
        )->withTimestamps();
    }
    
    public function fechasEspeciales()
    {
        return $this->hasMany(ClienteFechaEspecial::class, 'cliente_id');
    }
}
