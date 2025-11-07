<?php

namespace App\Domain\Core\Services;

use App\Domain\Core\Repositories\PersonaNaturalRepository;
use App\Domain\Core\Repositories\RolRepository;
use App\Domain\Core\Models\PersonaNatural;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class PersonaNaturalService
{
    protected $personaRepository;
    protected $auditoriaService;
    
    public function __construct(
        PersonaNaturalRepository $personaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->personaRepository = $personaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear persona natural con validaciones explícitas
     */
    public function crear(array $data): PersonaNatural
    {
        // Validar que no exista documento duplicado
        if ($this->personaRepository->existeDocumento($data['tipo_documento_id'], $data['numero_documento'])) {
            throw new BusinessException('Ya existe una persona con este tipo y número de documento');
        }
        
        // Validar email único si se proporciona
        if (isset($data['email']) && $this->personaRepository->existeEmail($data['email'])) {
            throw new BusinessException('El email ya está registrado');
        }
        
        DB::beginTransaction();
        try {
            // Crear persona
            $persona = $this->personaRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_personas_naturales',
                $persona->id,
                'INSERT',
                auth()->id(),
                null,
                $persona->toArray()
            );
            
            DB::commit();
            return $persona;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar persona natural
     */
    public function actualizar(int $id, array $data): PersonaNatural
    {
        // Validar que la persona existe
        $persona = $this->personaRepository->findByIdOrFail($id);
        
        // Validar documento único (excluyendo el actual)
        if (isset($data['tipo_documento_id']) && isset($data['numero_documento'])) {
            if ($this->personaRepository->existeDocumento(
                $data['tipo_documento_id'],
                $data['numero_documento'],
                $id
            )) {
                throw new BusinessException('Ya existe otra persona con este tipo y número de documento');
            }
        }
        
        // Validar email único (excluyendo el actual)
        if (isset($data['email']) && $this->personaRepository->existeEmail($data['email'], $id)) {
            throw new BusinessException('El email ya está registrado por otra persona');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $persona->toArray();
            
            // Actualizar
            $personaActualizada = $this->personaRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_personas_naturales',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $personaActualizada->toArray()
            );
            
            DB::commit();
            return $personaActualizada;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener persona por ID con relaciones
     */
    public function obtenerPorId(int $id): PersonaNatural
    {
        return $this->personaRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Persona no encontrada');
    }
    
    /**
     * Buscar por documento
     */
    public function buscarPorDocumento(int $tipoDocumentoId, string $numeroDocumento): ?PersonaNatural
    {
        return $this->personaRepository->findByDocumento($tipoDocumentoId, $numeroDocumento);
    }
    
    /**
     * Activar/Desactivar persona
     */
    public function cambiarEstado(int $id, bool $activo): PersonaNatural
    {
        $persona = $this->personaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $persona->toArray();
            
            $personaActualizada = $this->personaRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'core_personas_naturales',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $personaActualizada->toArray()
            );
            
            DB::commit();
            return $personaActualizada;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
