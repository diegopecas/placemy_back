<?php

namespace App\Domain\Restaurante\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Core\Models\PersonaJuridica;
use App\Domain\Core\Models\Ciudad;

class Restaurante extends Model
{
    protected $table = 'restaurantes';
    
    protected $fillable = [
        'persona_juridica_id',
        'nombre',
        'slug',
        'descripcion',
        'ciudad_id',
        'direccion',
        'telefono',
        'email_contacto',
        'logo_url',
        'banner_url',
        'tipo_cocina_id',
        'rango_precio_id',
        'capacidad_total',
        'horario_apertura',
        'url_menu',
        'calificacion_promedio',
        'num_resenas',
        'activo',
        'verificado',
        'fecha_apertura',
    ];
    
    protected $casts = [
        'horario_apertura' => 'array',
        'calificacion_promedio' => 'decimal:2',
        'activo' => 'boolean',
        'verificado' => 'boolean',
        'fecha_apertura' => 'date',
    ];
    
    // Relaciones
    public function personaJuridica()
    {
        return $this->belongsTo(PersonaJuridica::class, 'persona_juridica_id');
    }
    
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }
    
    public function tipoCocina()
    {
        return $this->belongsTo(TipoCocina::class, 'tipo_cocina_id');
    }
    
    public function rangoPrecio()
    {
        return $this->belongsTo(RangoPrecio::class, 'rango_precio_id');
    }
    
    public function metodosPago()
    {
        return $this->belongsToMany(
            MetodoPago::class,
            'restaurante_metodos_pago',
            'restaurante_id',
            'metodo_pago_id'
        )->withTimestamps();
    }
    
    public function caracteristicas()
    {
        return $this->belongsToMany(
            CaracteristicaRestaurante::class,
            'restaurante_caracteristicas',
            'restaurante_id',
            'caracteristica_id'
        )->withPivot('valor')
          ->withTimestamps();
    }
    
    public function gruposEmpresariales()
    {
        return $this->belongsToMany(
            GrupoEmpresarial::class,
            'grupo_empresarial_restaurantes',
            'restaurante_id',
            'grupo_empresarial_id'
        )->withTimestamps();
    }
    
    public function zonas()
    {
        return $this->hasMany(ZonaRestaurante::class, 'restaurante_id');
    }
    
    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'restaurante_id');
    }
    
    public function categorias()
    {
        return $this->hasMany(CategoriaMenu::class, 'restaurante_id');
    }
    
    public function platos()
    {
        return $this->belongsToMany(
            Plato::class,
            'restaurante_platos',
            'restaurante_id',
            'plato_id'
        )->withPivot('precio', 'disponible', 'calificacion_promedio', 'activo')
          ->withTimestamps();
    }
    
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'restaurante_productos',
            'restaurante_id',
            'producto_id'
        )->withPivot('precio_individual', 'disponible', 'activo')
          ->withTimestamps();
    }
    
    public function staff()
    {
        return $this->belongsToMany(
            Staff::class,
            'restaurante_staff',
            'restaurante_id',
            'staff_id'
        )->withPivot('cargo_id', 'usuario_id', 'fecha_asignacion', 'activo')
          ->withTimestamps();
    }
}
