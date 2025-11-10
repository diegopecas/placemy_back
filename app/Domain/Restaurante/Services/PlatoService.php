<?php

namespace App\Domain\Restaurante\Services;

use App\Domain\Restaurante\Contracts\PlatoServiceInterface;
use App\Domain\Restaurante\Repositories\PlatoRepository;
use App\Domain\Restaurante\Models\Plato;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class PlatoService implements PlatoServiceInterface
{
    protected $platoRepository;
    protected $auditoriaService;
    
    public function __construct(
        PlatoRepository $platoRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->platoRepository = $platoRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear plato con validaciones explícitas
     */
    public function crear(array $data): Plato
    {
        // Validar código único si se proporciona
        if (isset($data['codigo_plato']) && $this->platoRepository->existeCodigo($data['codigo_plato'])) {
            throw new BusinessException('Ya existe un plato con este código');
        }
        
        DB::beginTransaction();
        try {
            // Crear plato
            $plato = $this->platoRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'platos',
                $plato->id,
                'INSERT',
                auth()->id(),
                null,
                $plato->toArray()
            );
            
            DB::commit();
            return $plato;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar plato
     */
    public function actualizar(int $id, array $data): Plato
    {
        // Validar que el plato existe
        $plato = $this->platoRepository->findByIdOrFail($id);
        
        // Validar código único (excluyendo el actual)
        if (isset($data['codigo_plato']) && $this->platoRepository->existeCodigo($data['codigo_plato'], $id)) {
            throw new BusinessException('Ya existe otro plato con este código');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $plato->toArray();
            
            // Actualizar
            $platoActualizado = $this->platoRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'platos',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $platoActualizado->toArray()
            );
            
            DB::commit();
            return $platoActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener plato por ID con relaciones
     */
    public function obtenerPorId(int $id): Plato
    {
        return $this->platoRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Plato no encontrado');
    }
    
    /**
     * Asignar plato a restaurante
     */
    public function asignarARestaurante(int $platoId, int $restauranteId, array $data): void
    {
        $plato = $this->platoRepository->findByIdOrFail($platoId);
        
        DB::beginTransaction();
        try {
            // Validar que tenga precio
            if (!isset($data['precio']) || $data['precio'] <= 0) {
                throw new BusinessException('El precio es obligatorio y debe ser mayor a cero');
            }
            
            // Asignar plato al restaurante con pivot data
            $plato->restaurantes()->attach($restauranteId, [
                'precio' => $data['precio'],
                'disponible' => $data['disponible'] ?? true,
                'calificacion_promedio' => 0,
                'activo' => $data['activo'] ?? true,
            ]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurante_platos',
                $platoId,
                'INSERT',
                auth()->id(),
                null,
                array_merge($data, ['plato_id' => $platoId, 'restaurante_id' => $restauranteId])
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar plato en restaurante (precio, disponibilidad)
     */
    public function actualizarEnRestaurante(int $platoId, int $restauranteId, array $data): void
    {
        $plato = $this->platoRepository->findByIdOrFail($platoId);
        
        DB::beginTransaction();
        try {
            $datosActualizacion = [];
            
            if (isset($data['precio'])) {
                $datosActualizacion['precio'] = $data['precio'];
            }
            
            if (isset($data['disponible'])) {
                $datosActualizacion['disponible'] = $data['disponible'];
            }
            
            if (isset($data['activo'])) {
                $datosActualizacion['activo'] = $data['activo'];
            }
            
            if (empty($datosActualizacion)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            // Actualizar pivot
            $plato->restaurantes()->updateExistingPivot($restauranteId, $datosActualizacion);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurante_platos',
                $platoId,
                'UPDATE',
                auth()->id(),
                null,
                array_merge($datosActualizacion, ['plato_id' => $platoId, 'restaurante_id' => $restauranteId])
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Desasignar plato de restaurante
     */
    public function desasignarDeRestaurante(int $platoId, int $restauranteId): void
    {
        $plato = $this->platoRepository->findByIdOrFail($platoId);
        
        DB::beginTransaction();
        try {
            $plato->restaurantes()->detach($restauranteId);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurante_platos',
                $platoId,
                'DELETE',
                auth()->id(),
                ['plato_id' => $platoId, 'restaurante_id' => $restauranteId],
                null
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
