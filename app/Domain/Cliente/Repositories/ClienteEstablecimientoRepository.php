<?php

namespace App\Domain\Cliente\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cliente\Models\ClienteEstablecimiento;
use Illuminate\Database\Eloquent\Collection;

class ClienteEstablecimientoRepository extends BaseRepository
{
    public function __construct(ClienteEstablecimiento $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar por cliente y establecimiento
     */
    public function findByClienteYEstablecimiento(int $clienteId, int $establecimientoId): ?ClienteEstablecimiento
    {
        return $this->model::where('cliente_id', $clienteId)
            ->where('establecimiento_id', $establecimientoId)
            ->first();
    }
    
    /**
     * Verificar si existe asociación
     */
    public function existeAsociacion(int $clienteId, int $establecimientoId, ?int $excludeId = null): bool
    {
        $query = $this->model::where('cliente_id', $clienteId)
            ->where('establecimiento_id', $establecimientoId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar con relaciones
     */
    public function findByIdWithRelations(int $id): ?ClienteEstablecimiento
    {
        return $this->model::with([
            'cliente.persona',
            'establecimiento',
            'zonaPreferida',
            'canalesContacto.canalContacto',
            'campanias.campania',
            'resenas'
        ])->find($id);
    }
    
    /**
     * Listar por cliente
     */
    public function findByCliente(int $clienteId): Collection
    {
        return $this->model::where('cliente_id', $clienteId)
            ->with(['establecimiento', 'zonaPreferida'])
            ->get();
    }
    
    /**
     * Listar por establecimiento con filtros
     */
    public function findByEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        $query = $this->model::where('establecimiento_id', $establecimientoId)
            ->with(['cliente.persona', 'zonaPreferida']);
        
        // Filtro por calificación interna
        if (isset($filtros['calificacion_minima'])) {
            $query->where('calificacion_interna', '>=', $filtros['calificacion_minima']);
        }
        
        // Búsqueda por nombre
        if (isset($filtros['busqueda'])) {
            $busqueda = $filtros['busqueda'];
            $query->whereHas('cliente.persona', function($q) use ($busqueda) {
                $q->where('primer_nombre', 'like', "%{$busqueda}%")
                  ->orWhere('primer_apellido', 'like', "%{$busqueda}%")
                  ->orWhere('numero_documento', 'like', "%{$busqueda}%");
            });
        }
        
        return $query->get();
    }
}
