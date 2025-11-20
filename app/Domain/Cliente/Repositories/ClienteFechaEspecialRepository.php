<?php

namespace App\Domain\Cliente\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Cliente\Models\ClienteFechaEspecial;
use Illuminate\Database\Eloquent\Collection;

class ClienteFechaEspecialRepository extends BaseRepository
{
    public function __construct(ClienteFechaEspecial $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar por cliente
     */
    public function findByCliente(int $clienteId): Collection
    {
        return $this->model::where('cliente_id', $clienteId)
            ->with(['tipoFecha'])
            ->orderBy('fecha')
            ->get();
    }
    
    /**
     * Buscar con relaciones
     */
    public function findByIdWithRelations(int $id): ?ClienteFechaEspecial
    {
        return $this->model::with(['cliente.persona', 'tipoFecha'])->find($id);
    }
}
