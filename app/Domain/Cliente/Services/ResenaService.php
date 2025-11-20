<?php

namespace App\Domain\Cliente\Services;

use App\Domain\Cliente\Contracts\ResenaServiceInterface;
use App\Domain\Cliente\Repositories\ResenaRepository;
use App\Domain\Cliente\Models\Resena;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class ResenaService implements ResenaServiceInterface
{
    protected $resenaRepository;
    protected $auditoriaService;
    
    public function __construct(
        ResenaRepository $resenaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->resenaRepository = $resenaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar por cliente-establecimiento
     */
    public function listarPorClienteEstablecimiento(int $clienteEstablecimientoId): array
    {
        $resenas = $this->resenaRepository->findByClienteEstablecimiento($clienteEstablecimientoId);
        
        return $resenas->map(function ($resena) {
            return $this->formatearResena($resena);
        })->toArray();
    }
    
    /**
     * Listar por establecimiento
     */
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array
    {
        $resenas = $this->resenaRepository->findByEstablecimiento($establecimientoId, $filtros);
        
        return $resenas->map(function ($resena) {
            return $this->formatearResena($resena);
        })->toArray();
    }
    
    /**
     * Obtener por ID
     */
    public function obtenerPorId(int $id): array
    {
        $resena = $this->resenaRepository->findByIdWithRelations($id);
        
        if (!$resena) {
            throw new NotFoundException('Reseña no encontrada');
        }
        
        return $this->formatearResena($resena);
    }
    
    /**
     * Crear reseña
     */
    public function crear(array $data): array
    {
        // Validar calificación 1-5
        if (!isset($data['calificacion']) || $data['calificacion'] < 1 || $data['calificacion'] > 5) {
            throw new BusinessException('La calificación debe estar entre 1 y 5');
        }
        
        DB::beginTransaction();
        try {
            $datosCrear = [
                'cliente_establecimiento_id' => $data['cliente_establecimiento_id'],
                'calificacion' => $data['calificacion'],
                'comentario' => $data['comentario'] ?? null,
                'fecha_resena' => $data['fecha_resena'] ?? now()->format('Y-m-d'),
            ];
            
            $resena = $this->resenaRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'resenas',
                $resena->id,
                'INSERT',
                auth()->id(),
                null,
                $resena->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $resena = $this->resenaRepository->findByIdWithRelations($resena->id);
            return $this->formatearResena($resena);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar reseña
     */
    public function actualizar(int $id, array $data): array
    {
        $resena = $this->resenaRepository->findByIdOrFail($id);
        
        // Validar calificación si viene
        if (isset($data['calificacion']) && ($data['calificacion'] < 1 || $data['calificacion'] > 5)) {
            throw new BusinessException('La calificación debe estar entre 1 y 5');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $resena->toArray();
            
            $datosActualizar = [];
            
            if (isset($data['calificacion'])) {
                $datosActualizar['calificacion'] = $data['calificacion'];
            }
            
            if (isset($data['comentario'])) {
                $datosActualizar['comentario'] = $data['comentario'];
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            $resenaActualizada = $this->resenaRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'resenas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $resenaActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $resenaActualizada = $this->resenaRepository->findByIdWithRelations($id);
            return $this->formatearResena($resenaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Responder reseña
     */
    public function responder(int $id, string $respuesta): array
    {
        $resena = $this->resenaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $resena->toArray();
            
            $datosActualizar = [
                'respuesta_establecimiento' => $respuesta,
                'fecha_respuesta' => now()->format('Y-m-d'),
            ];
            
            $resenaActualizada = $this->resenaRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'resenas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $resenaActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $resenaActualizada = $this->resenaRepository->findByIdWithRelations($id);
            return $this->formatearResena($resenaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar reseña
     */
    public function eliminar(int $id): bool
    {
        $resena = $this->resenaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $resena->toArray();
            
            $this->resenaRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'resenas',
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
     * Formatear reseña para respuesta
     */
    private function formatearResena(Resena $resena): array
    {
        return [
            'id' => $resena->id,
            'cliente_establecimiento_id' => $resena->cliente_establecimiento_id,
            'calificacion' => $resena->calificacion,
            'comentario' => $resena->comentario,
            'fecha_resena' => $resena->fecha_resena->format('Y-m-d'),
            'respuesta_establecimiento' => $resena->respuesta_establecimiento,
            'fecha_respuesta' => $resena->fecha_respuesta?->format('Y-m-d'),
            'cliente' => $resena->clienteEstablecimiento && $resena->clienteEstablecimiento->cliente ? [
                'id' => $resena->clienteEstablecimiento->cliente->id,
                'sobrenombre' => $resena->clienteEstablecimiento->cliente->sobrenombre,
                'persona' => $resena->clienteEstablecimiento->cliente->persona ? [
                    'nombre_completo' => $resena->clienteEstablecimiento->cliente->persona->primer_nombre . ' ' . 
                                        $resena->clienteEstablecimiento->cliente->persona->primer_apellido,
                ] : null,
            ] : null,
            'establecimiento' => $resena->clienteEstablecimiento && $resena->clienteEstablecimiento->establecimiento ? [
                'id' => $resena->clienteEstablecimiento->establecimiento->id,
                'nombre' => $resena->clienteEstablecimiento->establecimiento->nombre,
            ] : null,
            'created_at' => $resena->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $resena->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
