<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaJuridica extends Model
{
    protected $table = 'core_personas_juridicas';
    
    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'razon_social',
        'nombre_comercial',
        'representante_legal_id',
        'ciudad_id',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'activo',
    ];
    
    protected $casts = [
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }
    
    public function representanteLegal()
    {
        return $this->belongsTo(PersonaNatural::class, 'representante_legal_id');
    }
    
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }
}
