<?php

namespace App\Domain\Restaurante\Services;

use App\Domain\Restaurante\Contracts\RestauranteServiceInterface;
use App\Domain\Restaurante\Repositories\RestauranteRepository;
use App\Domain\Restaurante\Models\Restaurante;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RestauranteService implements RestauranteServiceInterface
{
    protected $restauranteRepository;
    protected $auditoriaService;
    
    public function __construct(
        RestauranteRepository $restauranteRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->restauranteRepository = $restauranteRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Crear restaurante con validaciones explícitas
     */
    public function crear(array $data): Restaurante
    {
        // Generar slug si no se proporciona
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['nombre']);
        }
        
        // Validar que no exista slug duplicado
        if ($this->restauranteRepository->existeSlug($data['slug'])) {
            throw new BusinessException('Ya existe un restaurante con este slug');
        }
        
        DB::beginTransaction();
        try {
            // Crear restaurante
            $restaurante = $this->restauranteRepository->create($data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurantes',
                $restaurante->id,
                'INSERT',
                auth()->id(),
                null,
                $restaurante->toArray()
            );
            
            DB::commit();
            return $restaurante;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar restaurante
     */
    public function actualizar(int $id, array $data): Restaurante
    {
        // Validar que el restaurante existe
        $restaurante = $this->restauranteRepository->findByIdOrFail($id);
        
        // Validar slug único (excluyendo el actual)
        if (isset($data['slug']) && $this->restauranteRepository->existeSlug($data['slug'], $id)) {
            throw new BusinessException('Ya existe otro restaurante con este slug');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $restaurante->toArray();
            
            // Actualizar
            $restauranteActualizado = $this->restauranteRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurantes',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $restauranteActualizado->toArray()
            );
            
            DB::commit();
            return $restauranteActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener restaurante por ID con relaciones
     */
    public function obtenerPorId(int $id): Restaurante
    {
        return $this->restauranteRepository->findByIdWithRelations($id)
            ?? throw new BusinessException('Restaurante no encontrado');
    }
    
    /**
     * Obtener restaurante por slug
     */
    public function obtenerPorSlug(string $slug): Restaurante
    {
        return $this->restauranteRepository->findBySlug($slug)
            ?? throw new BusinessException('Restaurante no encontrado');
    }
    
    /**
     * Activar/Desactivar restaurante
     */
    public function cambiarEstado(int $id, bool $activo): Restaurante
    {
        $restaurante = $this->restauranteRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $restaurante->toArray();
            
            $restauranteActualizado = $this->restauranteRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurantes',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $restauranteActualizado->toArray()
            );
            
            DB::commit();
            return $restauranteActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Verificar restaurante
     */
    public function verificar(int $id, bool $verificado): Restaurante
    {
        $restaurante = $this->restauranteRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $restaurante->toArray();
            
            $restauranteActualizado = $this->restauranteRepository->update($id, ['verificado' => $verificado]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'restaurantes',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $restauranteActualizado->toArray()
            );
            
            DB::commit();
            return $restauranteActualizado;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
