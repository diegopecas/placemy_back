<?php

namespace App\Domain\Establecimiento\Models;

use Illuminate\Database\Eloquent\Model;
use App\Domain\Core\Models\PersonaJuridica;
use App\Domain\Core\Models\Ciudad;
use App\Domain\Core\Models\Rol;

class Establecimiento extends Model
{
    protected $table = 'establecimientos';
    
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
    
    // =====================================================
    // RELACIONES
    // =====================================================
    
    public function personaJuridica()
    {
        return $this->belongsTo(PersonaJuridica::class, 'persona_juridica_id');
    }
    
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }
    
    public function rangoPrecio()
    {
        return $this->belongsTo(RangoPrecio::class, 'rango_precio_id');
    }
    
    public function tiposCocina()
    {
        return $this->belongsToMany(
            TipoCocina::class,
            'establecimiento_tipos_cocina',
            'establecimiento_id',
            'tipo_cocina_id'
        )->withTimestamps();
    }
    
    public function metodosPago()
    {
        return $this->belongsToMany(
            MetodoPago::class,
            'establecimiento_metodos_pago',
            'establecimiento_id',
            'metodo_pago_id'
        )->withTimestamps();
    }
    
    public function caracteristicas()
    {
        return $this->belongsToMany(
            CaracteristicaEstablecimiento::class,
            'establecimiento_caracteristicas',
            'establecimiento_id',
            'caracteristica_id'
        )->withPivot('valor')
          ->withTimestamps();
    }
    
    public function gruposEmpresariales()
    {
        return $this->belongsToMany(
            GrupoEmpresarial::class,
            'grupo_empresarial_establecimientos',
            'establecimiento_id',
            'grupo_empresarial_id'
        )->withTimestamps();
    }
    
    public function zonas()
    {
        return $this->hasMany(ZonaEstablecimiento::class, 'establecimiento_id');
    }
    
    public function mesas()
    {
        return $this->hasMany(Mesa::class, 'establecimiento_id');
    }
    
    public function categorias()
    {
        return $this->hasMany(CategoriaMenu::class, 'establecimiento_id');
    }
    
    public function platos()
    {
        return $this->belongsToMany(
            Plato::class,
            'establecimiento_platos',
            'establecimiento_id',
            'plato_id'
        )->withPivot('precio', 'disponible', 'calificacion_promedio', 'activo')
          ->withTimestamps();
    }
    
    public function productos()
    {
        return $this->belongsToMany(
            Producto::class,
            'establecimiento_productos',
            'establecimiento_id',
            'producto_id'
        )->withPivot('precio_individual', 'disponible', 'activo')
          ->withTimestamps();
    }
    
    public function staff()
    {
        return $this->belongsToMany(
            Staff::class,
            'establecimiento_staff',
            'establecimiento_id',
            'staff_id'
        )->withPivot('cargo_id', 'usuario_id', 'fecha_asignacion', 'activo')
          ->withTimestamps();
    }
    
    public function roles()
    {
        return $this->hasMany(Rol::class, 'establecimiento_id');
    }
    
    public function cargos()
    {
        return $this->hasMany(Cargo::class, 'establecimiento_id');
    }
    
    public function configuraciones()
    {
        return $this->hasMany(EstablecimientoConfiguracion::class, 'establecimiento_id');
    }
}
