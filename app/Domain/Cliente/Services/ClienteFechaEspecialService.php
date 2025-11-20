<?php

namespace App\Domain\Cliente\Services;

use App\Domain\Cliente\Repositories\ClienteFechaEspecialRepository;
use App\Domain\Cliente\Models\ClienteFechaEspecial;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class ClienteFechaEspecialService
{
    protected $clienteFechaEspecialRepository;
    protected $auditoriaService;
    
    public function __construct(
        ClienteFechaEspecialRepository $clienteFechaEspecialRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->clienteFechaEspecialRepository = $clienteFechaEspecialRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar por cliente
     */
    public function listarPorCliente(int $clienteId): array
    {
        $fechas = $this->clienteFechaEspecialRepository->findByCliente($clienteId);
        
        return $fechas->map(function ($fecha) {
            return [
                'id' => $fecha->id,
                'cliente_id' => $fecha->cliente_id,
                'tipo_fecha_id' => $fecha->tipo_fecha_id,
                'fecha' => $fecha->fecha->format('Y-m-d'),
                'descripcion' => $fecha->descripcion,
                'tipo_fecha' => $fecha->tipoFecha ? [
                    'id' => $fecha->tipoFecha->id,
                    'nombre' => $fecha->tipoFecha->nombre,
                    'codigo' => $fecha->tipoFecha->codigo,
                    'icono' => $fecha->tipoFecha->icono,
                ] : null,
            ];
        })->toArray();
    }
    
    /**
     * Crear fecha especial
     */
    public function crear(array $data): array
    {
        DB::beginTransaction();
        try {
            $datosCrear = [
                'cliente_id' => $data['cliente_id'],
                'tipo_fecha_id' => $data['tipo_fecha_id'],
                'fecha' => $data['fecha'],
                'descripcion' => $data['descripcion'] ?? null,
            ];
            
            $fecha = $this->clienteFechaEspecialRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cliente_fechas_especiales',
                $fecha->id,
                'INSERT',
                auth()->id(),
                null,
                $fecha->toArray()
            );
            
            DB::commit();
            
            $fecha = $this->clienteFechaEspecialRepository->findByIdWithRelations($fecha->id);
            return [
                'id' => $fecha->id,
                'cliente_id' => $fecha->cliente_id,
                'tipo_fecha_id' => $fecha->tipo_fecha_id,
                'fecha' => $fecha->fecha->format('Y-m-d'),
                'descripcion' => $fecha->descripcion,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar fecha especial
     */
    public function actualizar(int $id, array $data): array
    {
        $fecha = $this->clienteFechaEspecialRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $fecha->toArray();
            
            $fechaActualizada = $this->clienteFechaEspecialRepository->update($id, $data);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cliente_fechas_especiales',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $fechaActualizada->toArray()
            );
            
            DB::commit();
            
            return [
                'id' => $fechaActualizada->id,
                'cliente_id' => $fechaActualizada->cliente_id,
                'tipo_fecha_id' => $fechaActualizada->tipo_fecha_id,
                'fecha' => $fechaActualizada->fecha->format('Y-m-d'),
                'descripcion' => $fechaActualizada->descripcion,
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar fecha especial
     */
    public function eliminar(int $id): bool
    {
        $fecha = $this->clienteFechaEspecialRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $fecha->toArray();
            
            $this->clienteFechaEspecialRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cliente_fechas_especiales',
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
}
