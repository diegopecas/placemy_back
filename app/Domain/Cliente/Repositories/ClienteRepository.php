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
     * Buscar cliente con relaciones completas
     */
    public function findByIdWithRelations(int $id): ?Cliente
    {
        return $this->model::with([
            'persona',
            'alergenos'
        ])->find($id);
    }
    
    /**
     * Buscar clientes con filtros
     * ACTUALIZADO: Busca en campos directos de clientes
     */
    public function findWithFilters(array $filtros = []): Collection
    {
        $query = $this->model::with(['persona']);
        
        // Búsqueda por teléfono, documento o nombre
        if (isset($filtros['busqueda'])) {
            $busqueda = $filtros['busqueda'];
            
            $query->where(function($q) use ($busqueda) {
                // Buscar en campos DIRECTOS de clientes
                $q->where('telefono', 'like', "%{$busqueda}%")
                  ->orWhere('numero_documento', 'like', "%{$busqueda}%")
                  ->orWhere('nombre', 'like', "%{$busqueda}%");
                
                // TAMBIÉN buscar en persona si existe
                $q->orWhereHas('persona', function($subQ) use ($busqueda) {
                    $subQ->where('telefono', 'like', "%{$busqueda}%")
                         ->orWhere('numero_documento', 'like', "%{$busqueda}%")
                         ->orWhere('primer_nombre', 'like', "%{$busqueda}%")
                         ->orWhere('primer_apellido', 'like', "%{$busqueda}%");
                });
            });
        }
        
        return $query->get();
    }
}