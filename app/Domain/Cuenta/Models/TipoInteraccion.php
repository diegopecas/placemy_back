<?php

namespace App\Domain\Cuenta\Models;

use Illuminate\Database\Eloquent\Model;

class TipoInteraccion extends Model
{
    protected $table = 'tipos_interacciones';
    
    protected $fillable = [
        'categoria_interaccion_id',
        'codigo',
        'nombre',
        'descripcion',
        'icono',
        'requiere_mensaje',
        'orden',
        'activo',
    ];
    
    protected $casts = [
        'requiere_mensaje' => 'boolean',
        'orden' => 'integer',
        'activo' => 'boolean',
    ];
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function categoria()
    {
        return $this->belongsTo(CategoriaInteraccion::class, 'categoria_interaccion_id');
    }
    
    public function cuentaInteracciones()
    {
        return $this->hasMany(CuentaInteraccion::class, 'tipo_interaccion_id');
    }
}
