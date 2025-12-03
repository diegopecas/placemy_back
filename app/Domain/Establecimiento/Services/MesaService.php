<?php

namespace App\Domain\Establecimiento\Services;

use App\Domain\Establecimiento\Contracts\MesaServiceInterface;
use App\Domain\Establecimiento\Repositories\MesaRepository;
use App\Domain\Establecimiento\Models\Mesa;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

class MesaService implements MesaServiceInterface
{
    protected $mesaRepository;
    protected $auditoriaService;
    
    public function __construct(
        MesaRepository $mesaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->mesaRepository = $mesaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar mesas por establecimiento con filtros opcionales
     */
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): Collection
    {
        return $this->mesaRepository->findByEstablecimiento($establecimientoId, $filtros);
    }
    
    /**
     * Obtener mesa por ID con relaciones
     */
    public function obtenerPorId(int $id): Mesa
    {
        return $this->mesaRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Mesa no encontrada');
    }
    
    /**
     * Crear mesa con validaciones explícitas
     */
    public function crear(array $data): Mesa
    {
        // Validar que no exista identificación de mesa duplicada en el establecimiento
        if ($this->mesaRepository->existeIdentificacionMesa($data['establecimiento_id'], $data['identificacion_mesa'])) {
            throw new BusinessException('Ya existe una mesa con esta identificación en el establecimiento');
        }
        
        DB::beginTransaction();
        try {
            // Crear mesa
            $mesa = $this->mesaRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'mesas',
                $mesa->id,
                'INSERT',
                auth()->id(),
                null,
                $mesa->toArray()
            );
            
            DB::commit();
            return $mesa;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar mesa
     */
    public function actualizar(int $id, array $data): Mesa
    {
        // Validar que la mesa existe
        $mesa = $this->mesaRepository->findByIdOrFail($id);
        
        // Validar identificación única (excluyendo la actual)
        if (isset($data['identificacion_mesa'])) {
            if ($this->mesaRepository->existeIdentificacionMesa(
                $data['establecimiento_id'] ?? $mesa->establecimiento_id,
                $data['identificacion_mesa'],
                $id
            )) {
                throw new BusinessException('Ya existe otra mesa con esta identificación en el establecimiento');
            }
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $mesa->toArray();
            
            // Actualizar
            $mesaActualizada = $this->mesaRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'mesas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $mesaActualizada->toArray()
            );
            
            DB::commit();
            return $mesaActualizada;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar mesa
     */
    public function eliminar(int $id): bool
    {
        $mesa = $this->mesaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $mesa->toArray();
            
            // Eliminar
            $resultado = $this->mesaRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'mesas',
                $id,
                'DELETE',
                auth()->id(),
                $datosAnteriores,
                null
            );
            
            DB::commit();
            return $resultado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar estado de mesa
     */
    public function cambiarEstado(int $id, int $estadoId): Mesa
    {
        $mesa = $this->mesaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $mesa->toArray();
            
            $mesaActualizada = $this->mesaRepository->update($id, ['estado_id' => $estadoId]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'mesas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $mesaActualizada->toArray()
            );
            
            DB::commit();
            return $mesaActualizada;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Asignar staff a mesa
     */
    public function asignarStaff(int $id, ?int $staffId): Mesa
    {
        $mesa = $this->mesaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $mesa->toArray();
            
            $mesaActualizada = $this->mesaRepository->update($id, ['staff_asignado_id' => $staffId]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'mesas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $mesaActualizada->toArray()
            );
            
            DB::commit();
            return $mesaActualizada;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}