<?php

namespace App\Domain\Cuenta\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cuenta\Models\Cuenta;
use Illuminate\Database\Eloquent\Collection;

class CuentaRepository extends BaseRepository
{
    public function __construct(Cuenta $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar cuenta por número de cuenta
     */
    public function findByNumeroCuenta(string $numeroCuenta): ?Cuenta
    {
        return $this->model::where('numero_cuenta', $numeroCuenta)->first();
    }
    
    /**
     * Verificar si existe número de cuenta
     */
    public function existeNumeroCuenta(string $numeroCuenta, ?int $excludeId = null): bool
    {
        $query = $this->model::where('numero_cuenta', $numeroCuenta);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar cuenta por palabra secreta
     */
    public function findByPalabraSecreta(string $palabraSecreta): ?Cuenta
    {
        return $this->model::where('palabra_secreta', $palabraSecreta)->first();
    }
    
    /**
     * Buscar cuentas por establecimiento
     */
    public function findByEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        $query = $this->model::where('establecimiento_id', $establecimientoId);
        
        // Filtro por estado
        if (isset($filtros['estado_id'])) {
            $query->where('estado_id', $filtros['estado_id']);
        }
        
        // Filtro por mesa
        if (isset($filtros['mesa_id'])) {
            $query->where('mesa_id', $filtros['mesa_id']);
        }
        
        // Filtro por mesero
        if (isset($filtros['establecimiento_staff_id'])) {
            $query->where('establecimiento_staff_id', $filtros['establecimiento_staff_id']);
        }
        
        // Filtro por cliente
        if (isset($filtros['cliente_id'])) {
            $query->where('cliente_id', $filtros['cliente_id']);
        }
        
        // Filtro por rango de fechas
        if (isset($filtros['fecha_desde'])) {
            $query->where('fecha_apertura', '>=', $filtros['fecha_desde']);
        }
        
        if (isset($filtros['fecha_hasta'])) {
            $query->where('fecha_apertura', '<=', $filtros['fecha_hasta']);
        }
        
        // Filtro por activo
        if (isset($filtros['activo'])) {
            $query->where('activo', $filtros['activo']);
        }
        
        return $query->with(['estado', 'mesa', 'mesero', 'cliente'])->get();
    }
    
    /**
     * Buscar cuenta activa de una mesa
     */
    public function findCuentaActivaMesa(int $mesaId): ?Cuenta
    {
        return $this->model::where('mesa_id', $mesaId)
            ->whereIn('estado_id', function($query) {
                $query->select('id')
                    ->from('cuenta_estados')
                    ->whereIn('codigo', ['ABIERTA', 'PAGADA']);
            })
            ->where('activo', true)
            ->first();
    }
    
    /**
     * Buscar cuenta con relaciones completas
     */
    public function findByIdWithRelations(int $id): ?Cuenta
    {
        return $this->model::with([
            'establecimiento',
            'mesa',
            'mesero.usuario.persona',
            'cerradoPor.usuario.persona',
            'cliente',
            'estado',
            'items.tipoItem',
            'items.plato.plato',
            'items.producto.producto',
            'items.estado',
            'impuestos.tipoImpuesto',
            'divisiones.itemsAsignados',
            'pagos.metodoPago',
            'interacciones.tipoInteraccion',
            'interacciones.estado'
        ])->find($id);
    }
    
    /**
     * Generar número de cuenta único
     */
    public function generarNumeroCuenta(int $establecimientoId): string
    {
        $fecha = now()->format('Y-m-d');
        
        // Buscar el último número del día
        $ultimaCuenta = $this->model::where('establecimiento_id', $establecimientoId)
            ->where('numero_cuenta', 'like', $fecha . '%')
            ->orderBy('numero_cuenta', 'desc')
            ->first();
        
        if ($ultimaCuenta) {
            // Extraer el número secuencial
            $partes = explode('-', $ultimaCuenta->numero_cuenta);
            $secuencial = intval(end($partes)) + 1;
        } else {
            $secuencial = 1;
        }
        
        return $fecha . '-' . str_pad($secuencial, 3, '0', STR_PAD_LEFT);
    }
}
