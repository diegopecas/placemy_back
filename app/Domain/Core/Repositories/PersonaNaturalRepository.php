<?php

namespace App\Domain\Core\Repositories;

use App\Domain\Shared\Repositories\BaseRepository;
use App\Domain\Core\Models\PersonaNatural;
use Illuminate\Database\Eloquent\Collection;

class PersonaNaturalRepository extends BaseRepository
{
    public function __construct(PersonaNatural $model)
    {
        $this->model = $model;
    }
    
    /**
     * Buscar persona por tipo y número de documento
     */
    public function findByDocumento(int $tipoDocumentoId, string $numeroDocumento): ?PersonaNatural
    {
        return $this->model::where('tipo_documento_id', $tipoDocumentoId)
            ->where('numero_documento', $numeroDocumento)
            ->first();
    }
    
    /**
     * Buscar persona por email
     */
    public function findByEmail(string $email): ?PersonaNatural
    {
        return $this->model::where('email', $email)->first();
    }
    
    /**
     * Buscar personas activas
     */
    public function findActivas(): Collection
    {
        return $this->model::where('activo', true)->get();
    }
    
    /**
     * Verificar si existe documento
     */
    public function existeDocumento(int $tipoDocumentoId, string $numeroDocumento, ?int $excludeId = null): bool
    {
        $query = $this->model::where('tipo_documento_id', $tipoDocumentoId)
            ->where('numero_documento', $numeroDocumento);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Verificar si existe email
     */
    public function existeEmail(string $email, ?int $excludeId = null): bool
    {
        $query = $this->model::where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }
    
    /**
     * Buscar con relaciones
     */
    public function findByIdWithRelations(int $id): ?PersonaNatural
    {
        return $this->model::with([
            'tipoDocumento',
            'ciudadNacimiento.departamento.pais',
            'ciudadResidencia.departamento.pais',
            'usuario.roles'
        ])->find($id);
    }
}
