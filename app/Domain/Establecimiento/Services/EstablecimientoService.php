<?php

namespace App\Domain\Establecimiento\Services;

use App\Domain\Establecimiento\Contracts\EstablecimientoServiceInterface;
use App\Domain\Establecimiento\Repositories\EstablecimientoRepository;
use App\Domain\Establecimiento\Models\Establecimiento;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EstablecimientoService implements EstablecimientoServiceInterface
{
    protected $establecimientoRepository;
    protected $auditoriaService;
    
    public function __construct(
        EstablecimientoRepository $establecimientoRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->establecimientoRepository = $establecimientoRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear establecimiento con validaciones explícitas
     */
    public function crear(array $data): Establecimiento
    {
        // Generar slug si no se proporciona
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['nombre']);
        }
        
        // Validar que no exista slug duplicado
        if ($this->establecimientoRepository->existeSlug($data['slug'])) {
            throw new BusinessException('Ya existe un establecimiento con este slug');
        }
        
        DB::beginTransaction();
        try {
            // Crear establecimiento
            $establecimiento = $this->establecimientoRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimientos',
                $establecimiento->id,
                'INSERT',
                auth()->id(),
                null,
                $establecimiento->toArray()
            );
            
            DB::commit();
            return $establecimiento;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar establecimiento
     */
    public function actualizar(int $id, array $data): Establecimiento
    {
        // Validar que el establecimiento existe
        $establecimiento = $this->establecimientoRepository->findByIdOrFail($id);
        
        // Validar slug único (excluyendo el actual)
        if (isset($data['slug']) && $this->establecimientoRepository->existeSlug($data['slug'], $id)) {
            throw new BusinessException('Ya existe otro establecimiento con este slug');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $establecimiento->toArray();
            
            // Actualizar
            $establecimientoActualizado = $this->establecimientoRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimientos',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $establecimientoActualizado->toArray()
            );
            
            DB::commit();
            return $establecimientoActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener establecimiento por ID con relaciones
     */
    public function obtenerPorId(int $id): Establecimiento
    {
        return $this->establecimientoRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Establecimiento no encontrado');
    }
    
    /**
     * Obtener establecimiento por slug
     */
    public function obtenerPorSlug(string $slug): Establecimiento
    {
        return $this->establecimientoRepository->findBySlug($slug)
            ?? throw new BusinessException('Establecimiento no encontrado');
    }
    
    /**
     * Activar/Desactivar establecimiento
     */
    public function cambiarEstado(int $id, bool $activo): Establecimiento
    {
        $establecimiento = $this->establecimientoRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $establecimiento->toArray();
            
            $establecimientoActualizado = $this->establecimientoRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimientos',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $establecimientoActualizado->toArray()
            );
            
            DB::commit();
            return $establecimientoActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Verificar establecimiento
     */
    public function verificar(int $id, bool $verificado): Establecimiento
    {
        $establecimiento = $this->establecimientoRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $establecimiento->toArray();
            
            $establecimientoActualizado = $this->establecimientoRepository->update($id, ['verificado' => $verificado]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'establecimientos',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $establecimientoActualizado->toArray()
            );
            
            DB::commit();
            return $establecimientoActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
