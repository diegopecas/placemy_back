<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;

class EstablecimientoConfiguracion extends Model
{
    protected $table = 'establecimiento_configuraciones';
    
    protected $fillable = [
        'establecimiento_id',
        'clave',
        'valor',
        'descripcion',
        'tipo',
        'categoria',
    ];
    
    // Relaciones
    public function establecimiento()
    {
        return $this->belongsTo(Establecimiento::class, 'establecimiento_id');
    }
    
    // =====================================================
    // MÉTODOS HELPER
    // =====================================================
    
    /**
     * Obtener valor parseado según el tipo
     */
    public function getValorParseado()
    {
        switch ($this->tipo) {
            case 'numero':
                return is_numeric($this->valor) ? floatval($this->valor) : null;
            case 'boolean':
                return filter_var($this->valor, FILTER_VALIDATE_BOOLEAN);
            case 'json':
                return json_decode($this->valor, true);
            default:
                return $this->valor;
        }
    }
}
