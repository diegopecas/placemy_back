<?php

namespace App\Domain\Cliente\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cliente\Models\Cliente;
use Illuminate\Database\Eloquent\Collection;

class ClienteRepository extends BaseRepository
{
    public function __construct(Cliente $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar cliente por persona_id
     */
    public function findByPersona(int $personaId): ?Cliente
    {
        return $this->model::where('persona_id', $personaId)->first();
    }
    
    /**
     * Verificar si existe un cliente con esa persona
     */
    public function existePersona(int $personaId, ?int $excludeId = null): bool
    {
        $query = $this->model::where('persona_id', $personaId);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar cliente con relaciones completas
     */
    public function findByIdWithRelations(int $id): ?Cliente
    {
        return $this->model::with([
            'persona',
            'establecimientos.establecimiento',
            'establecimientos.zonaPreferida',
            'alergenos',
            'fechasEspeciales.tipoFecha'
        ])->find($id);
    }
    
    /**
     * Buscar clientes con filtros
     */
    public function findWithFilters(array $filtros = []): Collection
    {
        $query = $this->model::with(['persona']);
        
        // Búsqueda por nombre, documento
        if (isset($filtros['busqueda'])) {
            $busqueda = $filtros['busqueda'];
            $query->whereHas('persona', function($q) use ($busqueda) {
                $q->where('primer_nombre', 'like', "%{$busqueda}%")
                  ->orWhere('primer_apellido', 'like', "%{$busqueda}%")
                  ->orWhere('numero_documento', 'like', "%{$busqueda}%");
            });
        }
        
        return $query->get();
    }
}
