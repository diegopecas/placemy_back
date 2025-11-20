<?php

namespace App\Domain\Cliente\Services;

use App\Domain\Cliente\Contracts\ClienteEstablecimientoServiceInterface;
use App\Domain\Cliente\Repositories\ClienteEstablecimientoRepository;
use App\Domain\Cliente\Models\ClienteEstablecimiento;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class ClienteEstablecimientoService implements ClienteEstablecimientoServiceInterface
{
    protected $clienteEstablecimientoRepository;
    protected $auditoriaService;
    
    public function __construct(
        ClienteEstablecimientoRepository $clienteEstablecimientoRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->clienteEstablecimientoRepository = $clienteEstablecimientoRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar por cliente
     */
    public function listarPorCliente(int $clienteId): array
    {
        $asociaciones = $this->clienteEstablecimientoRepository->findByCliente($clienteId);
        
        return $asociaciones->map(function ($item) {
            return $this->formatearAsociacion($item);
        })->toArray();
    }
    
    /**
     * Listar por establecimiento
     */
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array
    {
        $asociaciones = $this->clienteEstablecimientoRepository->findByEstablecimiento($establecimientoId, $filtros);
        
        return $asociaciones->map(function ($item) {
            return $this->formatearAsociacion($item);
        })->toArray();
    }
    
    /**
     * Obtener por ID
     */
    public function obtenerPorId(int $id): array
    {
        $asociacion = $this->clienteEstablecimientoRepository->findByIdWithRelations($id);
        
        if (!$asociacion) {
            throw new NotFoundException('Asociación cliente-establecimiento no encontrada');
        }
        
        return $this->formatearAsociacion($asociacion);
    }
    
    /**
     * Crear asociación cliente-establecimiento
     */
    public function crear(array $data): array
    {
        // Validar que no existe la asociación
        if ($this->clienteEstablecimientoRepository->existeAsociacion($data['cliente_id'], $data['establecimiento_id'])) {
            throw new BusinessException('El cliente ya está asociado a este establecimiento');
        }
        
        DB::beginTransaction();
        try {
            // Crear asociación
            $datosCrear = [
                'cliente_id' => $data['cliente_id'],
                'establecimiento_id' => $data['establecimiento_id'],
                'zona_preferida_id' => $data['zona_preferida_id'] ?? null,
                'notas_internas' => $data['notas_internas'] ?? null,
                'acepta_promociones' => $data['acepta_promociones'] ?? true,
                'fecha_primera_visita' => $data['fecha_primera_visita'] ?? now()->format('Y-m-d'),
                'calificacion_interna' => $data['calificacion_interna'] ?? null,
                'motivo_calificacion' => $data['motivo_calificacion'] ?? null,
            ];
            
            $asociacion = $this->clienteEstablecimientoRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cliente_establecimiento',
                $asociacion->id,
                'INSERT',
                auth()->id(),
                null,
                $asociacion->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $asociacion = $this->clienteEstablecimientoRepository->findByIdWithRelations($asociacion->id);
            return $this->formatearAsociacion($asociacion);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar asociación
     */
    public function actualizar(int $id, array $data): array
    {
        $asociacion = $this->clienteEstablecimientoRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $asociacion->toArray();
            
            // Actualizar
            $datosActualizar = [];
            
            if (isset($data['zona_preferida_id'])) {
                $datosActualizar['zona_preferida_id'] = $data['zona_preferida_id'];
            }
            
            if (isset($data['notas_internas'])) {
                $datosActualizar['notas_internas'] = $data['notas_internas'];
            }
            
            if (isset($data['acepta_promociones'])) {
                $datosActualizar['acepta_promociones'] = $data['acepta_promociones'];
            }
            
            if (isset($data['calificacion_interna'])) {
                $datosActualizar['calificacion_interna'] = $data['calificacion_interna'];
            }
            
            if (isset($data['motivo_calificacion'])) {
                $datosActualizar['motivo_calificacion'] = $data['motivo_calificacion'];
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            $asociacionActualizada = $this->clienteEstablecimientoRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cliente_establecimiento',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $asociacionActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $asociacionActualizada = $this->clienteEstablecimientoRepository->findByIdWithRelations($id);
            return $this->formatearAsociacion($asociacionActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar asociación
     */
    public function eliminar(int $id): bool
    {
        $asociacion = $this->clienteEstablecimientoRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $asociacion->toArray();
            
            // Eliminar
            $this->clienteEstablecimientoRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cliente_establecimiento',
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
     * Formatear asociación para respuesta
     */
    private function formatearAsociacion(ClienteEstablecimiento $asociacion): array
    {
        return [
            'id' => $asociacion->id,
            'cliente_id' => $asociacion->cliente_id,
            'establecimiento_id' => $asociacion->establecimiento_id,
            'zona_preferida_id' => $asociacion->zona_preferida_id,
            'notas_internas' => $asociacion->notas_internas,
            'acepta_promociones' => $asociacion->acepta_promociones,
            'fecha_primera_visita' => $asociacion->fecha_primera_visita?->format('Y-m-d'),
            'calificacion_interna' => $asociacion->calificacion_interna,
            'motivo_calificacion' => $asociacion->motivo_calificacion,
            'cliente' => $asociacion->cliente ? [
                'id' => $asociacion->cliente->id,
                'sobrenombre' => $asociacion->cliente->sobrenombre,
                'persona' => $asociacion->cliente->persona ? [
                    'id' => $asociacion->cliente->persona->id,
                    'nombre_completo' => $asociacion->cliente->persona->primer_nombre . ' ' . $asociacion->cliente->persona->primer_apellido,
                    'numero_documento' => $asociacion->cliente->persona->numero_documento,
                ] : null,
            ] : null,
            'establecimiento' => $asociacion->establecimiento ? [
                'id' => $asociacion->establecimiento->id,
                'nombre' => $asociacion->establecimiento->nombre,
            ] : null,
            'zona_preferida' => $asociacion->zonaPreferida ? [
                'id' => $asociacion->zonaPreferida->id,
                'nombre' => $asociacion->zonaPreferida->nombre,
            ] : null,
            'canales_contacto_count' => $asociacion->canalesContacto->count(),
            'campanias_count' => $asociacion->campanias->count(),
            'resenas_count' => $asociacion->resenas->count(),
            'created_at' => $asociacion->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $asociacion->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
