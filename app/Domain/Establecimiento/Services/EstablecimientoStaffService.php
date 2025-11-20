<?php

namespace App\Domain\Establecimiento\Services;

use App\Domain\Establecimiento\Contracts\EstablecimientoStaffServiceInterface;
use App\Domain\Establecimiento\Repositories\EstablecimientoStaffRepository;
use App\Domain\Establecimiento\Models\EstablecimientoStaff;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class EstablecimientoStaffService implements EstablecimientoStaffServiceInterface
{
    protected $staffRepository;
    protected $auditoriaService;
    
    public function __construct(
        EstablecimientoStaffRepository $staffRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->staffRepository = $staffRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar staff por establecimiento
     */
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array
    {
        $staff = $this->staffRepository->findByEstablecimiento($establecimientoId, $filtros);
        
        return $staff->map(function ($item) {
            return $this->formatearStaff($item);
        })->toArray();
    }
    
    /**
     * Obtener staff por ID
     */
    public function obtenerPorId(int $id): array
    {
        $staff = $this->staffRepository->findByIdWithRelations($id);
        
        if (!$staff) {
            throw new NotFoundException('Staff no encontrado');
        }
        
        return $this->formatearStaff($staff);
    }
    
    /**
     * Crear staff en establecimiento
     */
    public function crear(array $data): array
    {
        // Validar que el usuario no esté ya asignado en el establecimiento
        if ($this->staffRepository->existeUsuarioEnEstablecimiento($data['usuario_id'], $data['establecimiento_id'])) {
            throw new BusinessException('Este usuario ya está asignado como staff en este establecimiento');
        }
        
        // Validar código de empleado único en el establecimiento
        if ($this->staffRepository->existeCodigo($data['codigo_empleado'], $data['establecimiento_id'])) {
            throw new BusinessException('Ya existe un empleado con este código en el establecimiento');
        }
        
        DB::beginTransaction();
        try {
            // Preparar datos
            $datosCrear = [
                'establecimiento_id' => $data['establecimiento_id'],
                'cargo_id' => $data['cargo_id'],
                'usuario_id' => $data['usuario_id'],
                'codigo_empleado' => $data['codigo_empleado'],
                'fecha_asignacion' => $data['fecha_asignacion'] ?? now()->format('Y-m-d'),
                'activo' => $data['activo'] ?? true,
            ];
            
            // Crear staff
            $staff = $this->staffRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $staff->id,
                'INSERT',
                auth()->id(),
                null,
                $staff->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $staff = $this->staffRepository->findByIdWithRelations($staff->id);
            return $this->formatearStaff($staff);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar staff
     */
    public function actualizar(int $id, array $data): array
    {
        $staff = $this->staffRepository->findByIdOrFail($id);
        
        // Validar código único (excluyendo el actual)
        if (isset($data['codigo_empleado'])) {
            if ($this->staffRepository->existeCodigo($data['codigo_empleado'], $staff->establecimiento_id, $id)) {
                throw new BusinessException('Ya existe otro empleado con este código en el establecimiento');
            }
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $staff->toArray();
            
            // Preparar datos a actualizar
            $datosActualizar = [];
            
            if (isset($data['cargo_id'])) {
                $datosActualizar['cargo_id'] = $data['cargo_id'];
            }
            
            if (isset($data['codigo_empleado'])) {
                $datosActualizar['codigo_empleado'] = $data['codigo_empleado'];
            }
            
            if (isset($data['activo'])) {
                $datosActualizar['activo'] = $data['activo'];
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            // Actualizar
            $staffActualizado = $this->staffRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $staffActualizado->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $staffActualizado = $this->staffRepository->findByIdWithRelations($id);
            return $this->formatearStaff($staffActualizado);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar staff (soft delete - cambiar a inactivo)
     */
    public function eliminar(int $id): bool
    {
        $staff = $this->staffRepository->findByIdOrFail($id);
        
        // Verificar si tiene mesas asignadas
        if ($staff->mesasAsignadas()->where('activo', true)->count() > 0) {
            throw new BusinessException('No se puede eliminar el staff porque tiene mesas asignadas');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $staff->toArray();
            
            // Marcar como inactivo
            $this->staffRepository->update($id, ['activo' => false]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $id,
                'DELETE',
                auth()->id(),
                $datosAnteriores,
                null
            );
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar estado activo/inactivo
     */
    public function cambiarEstado(int $id, bool $activo): array
    {
        $staff = $this->staffRepository->findByIdOrFail($id);
        
        // Si se va a desactivar, verificar que no tenga mesas asignadas
        if (!$activo && $staff->mesasAsignadas()->where('activo', true)->count() > 0) {
            throw new BusinessException('No se puede desactivar el staff porque tiene mesas asignadas');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $staff->toArray();
            
            $staffActualizado = $this->staffRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimiento_staff',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $staffActualizado->toArray()
            );
            
            DB::commit();
            
            $staffActualizado = $this->staffRepository->findByIdWithRelations($id);
            return $this->formatearStaff($staffActualizado);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener staff por cargo en un establecimiento
     */
    public function obtenerPorCargo(int $establecimientoId, int $cargoId): array
    {
        $staff = $this->staffRepository->findByEstablecimientoYCargo($establecimientoId, $cargoId);
        
        return $staff->map(function ($item) {
            return $this->formatearStaff($item);
        })->toArray();
    }
    
    /**
     * Formatear staff para respuesta
     */
    private function formatearStaff(EstablecimientoStaff $staff): array
    {
        return [
            'id' => $staff->id,
            'establecimiento_id' => $staff->establecimiento_id,
            'codigo_empleado' => $staff->codigo_empleado,
            'fecha_asignacion' => $staff->fecha_asignacion?->format('Y-m-d'),
            'activo' => $staff->activo,
            'cargo' => $staff->cargo ? [
                'id' => $staff->cargo->id,
                'nombre' => $staff->cargo->nombre,
            ] : null,
            'usuario' => $staff->usuario ? [
                'id' => $staff->usuario->id,
                'username' => $staff->usuario->username,
                'email' => $staff->usuario->email,
                'persona' => $staff->usuario->persona ? [
                    'id' => $staff->usuario->persona->id,
                    'nombre_completo' => $staff->usuario->persona->nombre_completo,
                    'numero_documento' => $staff->usuario->persona->numero_documento,
                    'telefono' => $staff->usuario->persona->telefono,
                ] : null,
            ] : null,
            'mesas_asignadas' => $staff->mesasAsignadas ? $staff->mesasAsignadas->count() : 0,
            'created_at' => $staff->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $staff->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}