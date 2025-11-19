<?php

namespace App\Domain\Establecimiento\Services;

use App\Domain\Establecimiento\Contracts\StaffServiceInterface;
use App\Domain\Establecimiento\Repositories\StaffRepository;
use App\Domain\Establecimiento\Models\Staff;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class StaffService implements StaffServiceInterface
{
    protected $staffRepository;
    protected $auditoriaService;
    
    public function __construct(
        StaffRepository $staffRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->staffRepository = $staffRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear staff con validaciones explícitas
     */
    public function crear(array $data): Staff
    {
        // Validar código de empleado único
        if ($this->staffRepository->existeCodigo($data['codigo_empleado'])) {
            throw new BusinessException('Ya existe un empleado con este código');
        }
        
        // Validar que la persona no esté ya registrada como staff
        if ($this->staffRepository->findByPersona($data['persona_id'])) {
            throw new BusinessException('Esta persona ya está registrada como staff');
        }
        
        DB::beginTransaction();
        try {
            // Crear staff
            $staff = $this->staffRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'staff',
                $staff->id,
                'INSERT',
                auth()->id(),
                null,
                $staff->toArray()
            );
            
            DB::commit();
            return $staff;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar staff
     */
    public function actualizar(int $id, array $data): Staff
    {
        // Validar que el staff existe
        $staff = $this->staffRepository->findByIdOrFail($id);
        
        // Validar código único (excluyendo el actual)
        if (isset($data['codigo_empleado']) && $this->staffRepository->existeCodigo($data['codigo_empleado'], $id)) {
            throw new BusinessException('Ya existe otro empleado con este código');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $staff->toArray();
            
            // Actualizar
            $staffActualizado = $this->staffRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'staff',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $staffActualizado->toArray()
            );
            
            DB::commit();
            return $staffActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener staff por ID con relaciones
     */
    public function obtenerPorId(int $id): Staff
    {
        return $this->staffRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Staff no encontrado');
    }
    
    /**
     * Asignar staff a establecimiento
     */
    public function asignarAEstablecimiento(int $staffId, int $establecimientoId, array $data): void
    {
        $staff = $this->staffRepository->findByIdOrFail($staffId);
        
        DB::beginTransaction();
        try {
            // Validar datos requeridos
            if (!isset($data['cargo_id']) || !isset($data['usuario_id'])) {
                throw new BusinessException('El cargo y el usuario son obligatorios');
            }
            
            // Asignar staff al establecimiento
            $staff->establecimientos()->attach($establecimientoId, [
                'cargo_id' => $data['cargo_id'],
                'usuario_id' => $data['usuario_id'],
                'fecha_asignacion' => $data['fecha_asignacion'] ?? now()->format('Y-m-d'),
                'activo' => $data['activo'] ?? true,
            ]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $staffId,
                'INSERT',
                auth()->id(),
                null,
                array_merge($data, ['staff_id' => $staffId, 'establecimiento_id' => $establecimientoId])
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar asignación de staff en establecimiento
     */
    public function actualizarEnEstablecimiento(int $staffId, int $establecimientoId, array $data): void
    {
        $staff = $this->staffRepository->findByIdOrFail($staffId);
        
        DB::beginTransaction();
        try {
            $datosActualizacion = [];
            
            if (isset($data['cargo_id'])) {
                $datosActualizacion['cargo_id'] = $data['cargo_id'];
            }
            
            if (isset($data['activo'])) {
                $datosActualizacion['activo'] = $data['activo'];
            }
            
            if (empty($datosActualizacion)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            // Actualizar pivot
            $staff->establecimientos()->updateExistingPivot($establecimientoId, $datosActualizacion);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $staffId,
                'UPDATE',
                auth()->id(),
                null,
                array_merge($datosActualizacion, ['staff_id' => $staffId, 'establecimiento_id' => $establecimientoId])
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Desasignar staff de establecimiento
     */
    public function desasignarDeEstablecimiento(int $staffId, int $establecimientoId): void
    {
        $staff = $this->staffRepository->findByIdOrFail($staffId);
        
        DB::beginTransaction();
        try {
            $staff->establecimientos()->detach($establecimientoId);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $staffId,
                'DELETE',
                auth()->id(),
                ['staff_id' => $staffId, 'establecimiento_id' => $establecimientoId],
                null
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar estado activo/inactivo
     */
    public function cambiarEstado(int $id, bool $activo): Staff
    {
        $staff = $this->staffRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $staff->toArray();
            
            $staffActualizado = $this->staffRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'staff',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $staffActualizado->toArray()
            );
            
            DB::commit();
            return $staffActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
