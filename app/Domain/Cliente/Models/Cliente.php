<?php

namespace App\Domain\Cliente\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Core\Models\PersonaNatural;
use App\Domain\Establecimiento\Models\Alergeno;

class Cliente extends Model
{
    protected $table = 'clientes';
    
    protected $fillable = [
        // NUEVOS CAMPOS DIRECTOS
        'nombre',
        'tipo_documento_id',
        'numero_documento',
        'telefono',
        'email',
        'sexo',
        'dia_cumpleanos',
        'mes_cumpleanos',
        
        // CAMPOS EXISTENTES
        'persona_id',  // Ahora OPCIONAL
        'sobrenombre',
        'preferencias_gustos',
        'preferencias_no_gustos',
        'otras_alergias',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    /**
     * Relación con PersonaNatural (OPCIONAL)
     */
    public function persona()
    {
        return $this->belongsTo(PersonaNatural::class, 'persona_id');
    }
    
    /**
     * Relación con establecimientos
     */
    public function establecimientos()
    {
        return $this->hasMany(ClienteEstablecimiento::class, 'cliente_id');
    }
    
    /**
     * Relación muchos a muchos con alergenos
     */
    public function alergenos()
    {
        return $this->belongsToMany(
            Alergeno::class,
            'cliente_alergenos',
            'cliente_id',
            'alergeno_id'
        )->withTimestamps();
    }
    
    /**
     * Relación con fechas especiales
     */
    public function fechasEspeciales()
    {
        return $this->hasMany(ClienteFechaEspecial::class, 'cliente_id');
    }
}