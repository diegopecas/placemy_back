<?php

namespace App\Domain\Cliente\Services;

use App\Domain\Cliente\Contracts\CampaniaServiceInterface;
use App\Domain\Cliente\Repositories\CampaniaRepository;
use App\Domain\Cliente\Models\Campania;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class CampaniaService implements CampaniaServiceInterface
{
    protected $campaniaRepository;
    protected $auditoriaService;
    
    public function __construct(
        CampaniaRepository $campaniaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->campaniaRepository = $campaniaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar por establecimiento
     */
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array
    {
        $campanias = $this->campaniaRepository->findByEstablecimiento($establecimientoId, $filtros);
        
        return $campanias->map(function ($campania) {
            return $this->formatearCampania($campania);
        })->toArray();
    }
    
    /**
     * Obtener por ID
     */
    public function obtenerPorId(int $id): array
    {
        $campania = $this->campaniaRepository->findByIdWithRelations($id);
        
        if (!$campania) {
            throw new NotFoundException('Campaña no encontrada');
        }
        
        return $this->formatearCampania($campania);
    }
    
    /**
     * Crear campaña
     */
    public function crear(array $data): array
    {
        DB::beginTransaction();
        try {
            $datosCrear = [
                'establecimiento_id' => $data['establecimiento_id'],
                'tipo_campania_id' => $data['tipo_campania_id'],
                'nombre' => $data['nombre'],
                'descripcion' => $data['descripcion'] ?? null,
                'icono' => $data['icono'] ?? null,
                'fecha_inicio' => $data['fecha_inicio'],
                'fecha_fin' => $data['fecha_fin'],
                'configuracion_json' => $data['configuracion_json'] ?? null,
                'activo' => $data['activo'] ?? true,
            ];
            
            $campania = $this->campaniaRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'campanias',
                $campania->id,
                'INSERT',
                auth()->id(),
                null,
                $campania->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $campania = $this->campaniaRepository->findByIdWithRelations($campania->id);
            return $this->formatearCampania($campania);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar campaña
     */
    public function actualizar(int $id, array $data): array
    {
        $campania = $this->campaniaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $campania->toArray();
            
            $datosActualizar = [];
            
            foreach (['nombre', 'descripcion', 'icono', 'fecha_inicio', 'fecha_fin', 'configuracion_json', 'activo'] as $campo) {
                if (isset($data[$campo])) {
                    $datosActualizar[$campo] = $data[$campo];
                }
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            $campaniaActualizada = $this->campaniaRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'campanias',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $campaniaActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $campaniaActualizada = $this->campaniaRepository->findByIdWithRelations($id);
            return $this->formatearCampania($campaniaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar campaña
     */
    public function eliminar(int $id): bool
    {
        $campania = $this->campaniaRepository->findByIdOrFail($id);
        
        // Verificar que no tenga clientes inscritos
        if ($campania->clientesCampanias()->count() > 0) {
            throw new BusinessException('No se puede eliminar la campaña porque tiene clientes inscritos');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $campania->toArray();
            
            $this->campaniaRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'campanias',
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
        $campania = $this->campaniaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $campania->toArray();
            
            $campaniaActualizada = $this->campaniaRepository->update($id, ['activo' => $activo]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'campanias',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $campaniaActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $campaniaActualizada = $this->campaniaRepository->findByIdWithRelations($id);
            return $this->formatearCampania($campaniaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Formatear campaña para respuesta
     */
    private function formatearCampania(Campania $campania): array
    {
        return [
            'id' => $campania->id,
            'establecimiento_id' => $campania->establecimiento_id,
            'tipo_campania_id' => $campania->tipo_campania_id,
            'nombre' => $campania->nombre,
            'descripcion' => $campania->descripcion,
            'icono' => $campania->icono,
            'fecha_inicio' => $campania->fecha_inicio->format('Y-m-d'),
            'fecha_fin' => $campania->fecha_fin->format('Y-m-d'),
            'configuracion_json' => $campania->configuracion_json,
            'activo' => $campania->activo,
            'establecimiento' => $campania->establecimiento ? [
                'id' => $campania->establecimiento->id,
                'nombre' => $campania->establecimiento->nombre,
            ] : null,
            'tipo_campania' => $campania->tipoCampania ? [
                'id' => $campania->tipoCampania->id,
                'nombre' => $campania->tipoCampania->nombre,
                'codigo' => $campania->tipoCampania->codigo,
            ] : null,
            'clientes_inscritos' => $campania->clientesCampanias->count(),
            'created_at' => $campania->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $campania->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
