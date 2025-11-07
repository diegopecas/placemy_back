<?php

namespace App\Domain\Core\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaNatural extends Model
{
    protected $table = 'core_personas_naturales';
    
    protected $fillable = [
        'tipo_documento_id',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'sexo',
        'ciudad_nacimiento_id',
        'ciudad_residencia_id',
        'direccion',
        'telefono',
        'email',
        'activo',
    ];
    
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'activo' => 'boolean',
    ];
    
    // Relaciones
    public function tipoDocumento()
    {
        return $this->belongsTo(TipoDocumento::class, 'tipo_documento_id');
    }
    
    public function ciudadNacimiento()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_nacimiento_id');
    }
    
    public function ciudadResidencia()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_residencia_id');
    }
    
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'persona_id');
    }
    
    // Accessor para nombre completo
    public function getNombreCompletoAttribute(): string
    {
        $nombres = trim($this->primer_nombre . ' ' . $this->segundo_nombre);
        $apellidos = trim($this->primer_apellido . ' ' . $this->segundo_apellido);
        return trim($nombres . ' ' . $apellidos);
    }
}
