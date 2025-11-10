<?php

namespace App\Domain\Restaurante\Services;

use App\Domain\Restaurante\Contracts\ProductoServiceInterface;
use App\Domain\Restaurante\Repositories\ProductoRepository;
use App\Domain\Restaurante\Models\Producto;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class ProductoService implements ProductoServiceInterface
{
    protected $productoRepository;
    protected $auditoriaService;
    
    public function __construct(
        ProductoRepository $productoRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->productoRepository = $productoRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear producto con validaciones explícitas
     */
    public function crear(array $data): Producto
    {
        // Validar código único si se proporciona
        if (isset($data['codigo_producto']) && $this->productoRepository->existeCodigo($data['codigo_producto'])) {
            throw new BusinessException('Ya existe un producto con este código');
        }
        
        DB::beginTransaction();
        try {
            // Crear producto
            $producto = $this->productoRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'productos',
                $producto->id,
                'INSERT',
                auth()->id(),
                null,
                $producto->toArray()
            );
            
            DB::commit();
            return $producto;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar producto
     */
    public function actualizar(int $id, array $data): Producto
    {
        // Validar que el producto existe
        $producto = $this->productoRepository->findByIdOrFail($id);
        
        // Validar código único (excluyendo el actual)
        if (isset($data['codigo_producto']) && $this->productoRepository->existeCodigo($data['codigo_producto'], $id)) {
            throw new BusinessException('Ya existe otro producto con este código');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $producto->toArray();
            
            // Actualizar
            $productoActualizado = $this->productoRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'productos',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $productoActualizado->toArray()
            );
            
            DB::commit();
            return $productoActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener producto por ID con relaciones
     */
    public function obtenerPorId(int $id): Producto
    {
        return $this->productoRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Producto no encontrado');
    }
    
    /**
     * Asignar producto a restaurante
     */
    public function asignarARestaurante(int $productoId, int $restauranteId, array $data): void
    {
        $producto = $this->productoRepository->findByIdOrFail($productoId);
        
        DB::beginTransaction();
        try {
            // Asignar producto al restaurante con pivot data
            $producto->restaurantes()->attach($restauranteId, [
                'precio_individual' => $data['precio_individual'] ?? null,
                'disponible' => $data['disponible'] ?? true,
                'activo' => $data['activo'] ?? true,
            ]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurante_productos',
                $productoId,
                'INSERT',
                auth()->id(),
                null,
                array_merge($data, ['producto_id' => $productoId, 'restaurante_id' => $restauranteId])
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar producto en restaurante (precio, disponibilidad)
     */
    public function actualizarEnRestaurante(int $productoId, int $restauranteId, array $data): void
    {
        $producto = $this->productoRepository->findByIdOrFail($productoId);
        
        DB::beginTransaction();
        try {
            $datosActualizacion = [];
            
            if (isset($data['precio_individual'])) {
                $datosActualizacion['precio_individual'] = $data['precio_individual'];
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
            $producto->restaurantes()->updateExistingPivot($restauranteId, $datosActualizacion);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurante_productos',
                $productoId,
                'UPDATE',
                auth()->id(),
                null,
                array_merge($datosActualizacion, ['producto_id' => $productoId, 'restaurante_id' => $restauranteId])
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Desasignar producto de restaurante
     */
    public function desasignarDeRestaurante(int $productoId, int $restauranteId): void
    {
        $producto = $this->productoRepository->findByIdOrFail($productoId);
        
        DB::beginTransaction();
        try {
            $producto->restaurantes()->detach($restauranteId);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurante_productos',
                $productoId,
                'DELETE',
                auth()->id(),
                ['producto_id' => $productoId, 'restaurante_id' => $restauranteId],
                null
            );
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
