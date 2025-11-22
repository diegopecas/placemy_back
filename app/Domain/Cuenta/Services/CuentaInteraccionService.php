<?php

namespace App\Domain\Cuenta\Services;

use App\Domain\Cuenta\Contracts\CuentaInteraccionServiceInterface;
use App\Domain\Cuenta\Repositories\CuentaInteraccionRepository;
use App\Domain\Cuenta\Repositories\CuentaRepository;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class CuentaInteraccionService implements CuentaInteraccionServiceInterface
{
    protected $interaccionRepository;
    protected $cuentaRepository;
    protected $auditoriaService;
    
    public function __construct(
        CuentaInteraccionRepository $interaccionRepository,
        CuentaRepository $cuentaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->interaccionRepository = $interaccionRepository;
        $this->cuentaRepository = $cuentaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar interacciones por cuenta
     */
    public function listarPorCuenta(int $cuentaId): array
    {
        $interacciones = $this->interaccionRepository->findByCuenta($cuentaId);
        
        return $interacciones->map(function ($interaccion) {
            return $this->formatearInteraccion($interaccion);
        })->toArray();
    }
    
    /**
     * Listar interacciones pendientes por establecimiento
     */
    public function listarPendientesPorEstablecimiento(int $establecimientoId): array
    {
        $interacciones = $this->interaccionRepository->findPendientesByEstablecimiento($establecimientoId);
        
        return $interacciones->map(function ($interaccion) {
            return $this->formatearInteraccion($interaccion);
        })->toArray();
    }
    
    /**
     * Obtener interacción por ID
     */
    public function obtenerPorId(int $id): array
    {
        $interaccion = $this->interaccionRepository->findByIdWithRelations($id);
        
        if (!$interaccion) {
            throw new NotFoundException('Interacción no encontrada');
        }
        
        return $this->formatearInteraccionCompleta($interaccion);
    }
    
    /**
     * Crear interacción
     */
    public function crear(array $data): array
    {
        // Validar que la cuenta existe
        $this->cuentaRepository->findByIdOrFail($data['cuenta_id']);
        
        DB::beginTransaction();
        try {
            // Buscar estado PENDIENTE
            $estadoPendiente = DB::table('interaccion_estados')
                ->where('codigo', 'PENDIENTE')
                ->first();
            
            // Preparar datos
            $datosCrear = [
                'cuenta_id' => $data['cuenta_id'],
                'tipo_interaccion_id' => $data['tipo_interaccion_id'],
                'estado_id' => $estadoPendiente->id,
                'valor_numerico' => $data['valor_numerico'] ?? null,
                'mensaje' => $data['mensaje'] ?? null,
                'opcion_seleccionada' => $data['opcion_seleccionada'] ?? null,
                'foto_url' => $data['foto_url'] ?? null,
                'fecha_interaccion' => $data['fecha_interaccion'] ?? now(),
                'fecha_atencion' => null,
                'atendido_por_staff_id' => null,
                'notas_atencion' => null,
            ];
            
            // Crear interacción
            $interaccion = $this->interaccionRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_interacciones',
                $interaccion->id,
                'INSERT',
                auth()->id(),
                null,
                $interaccion->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $interaccion = $this->interaccionRepository->findByIdWithRelations($interaccion->id);
            return $this->formatearInteraccionCompleta($interaccion);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Atender interacción
     */
    public function atender(int $id, int $staffId, string $notas = null): array
    {
        $interaccion = $this->interaccionRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $interaccion->toArray();
            
            // Buscar estado ATENDIDA
            $estadoAtendida = DB::table('interaccion_estados')
                ->where('codigo', 'ATENDIDA')
                ->first();
            
            // Actualizar interacción
            $datosActualizar = [
                'estado_id' => $estadoAtendida->id,
                'fecha_atencion' => now(),
                'atendido_por_staff_id' => $staffId,
                'notas_atencion' => $notas,
            ];
            
            $interaccionActualizada = $this->interaccionRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_interacciones',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $interaccionActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $interaccionActualizada = $this->interaccionRepository->findByIdWithRelations($id);
            return $this->formatearInteraccionCompleta($interaccionActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar estado de interacción
     */
    public function cambiarEstado(int $id, int $estadoId): array
    {
        $interaccion = $this->interaccionRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $interaccion->toArray();
            
            $interaccionActualizada = $this->interaccionRepository->update($id, ['estado_id' => $estadoId]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_interacciones',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $interaccionActualizada->toArray()
            );
            
            DB::commit();
            
            $interaccionActualizada = $this->interaccionRepository->findByIdWithRelations($id);
            return $this->formatearInteraccionCompleta($interaccionActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Formatear interacción simple
     */
    private function formatearInteraccion($interaccion): array
    {
        return [
            'id' => $interaccion->id,
            'cuenta_id' => $interaccion->cuenta_id,
            'mesa' => $interaccion->cuenta && $interaccion->cuenta->mesa ? [
                'id' => $interaccion->cuenta->mesa->id,
                'identificacion_mesa' => $interaccion->cuenta->mesa->identificacion_mesa,
            ] : null,
            'tipo_interaccion' => $interaccion->tipoInteraccion ? [
                'id' => $interaccion->tipoInteraccion->id,
                'codigo' => $interaccion->tipoInteraccion->codigo,
                'nombre' => $interaccion->tipoInteraccion->nombre,
                'icono' => $interaccion->tipoInteraccion->icono,
                'requiere_mensaje' => $interaccion->tipoInteraccion->requiere_mensaje,
                'categoria' => $interaccion->tipoInteraccion->categoria ? [
                    'id' => $interaccion->tipoInteraccion->categoria->id,
                    'nombre' => $interaccion->tipoInteraccion->categoria->nombre,
                    'icono' => $interaccion->tipoInteraccion->categoria->icono,
                ] : null,
            ] : null,
            'estado' => $interaccion->estado ? [
                'id' => $interaccion->estado->id,
                'codigo' => $interaccion->estado->codigo,
                'nombre' => $interaccion->estado->nombre,
                'color' => $interaccion->estado->color,
                'icono' => $interaccion->estado->icono,
            ] : null,
            'valor_numerico' => $interaccion->valor_numerico,
            'mensaje' => $interaccion->mensaje,
            'opcion_seleccionada' => $interaccion->opcion_seleccionada,
            'fecha_interaccion' => $interaccion->fecha_interaccion?->format('Y-m-d H:i:s'),
            'fecha_atencion' => $interaccion->fecha_atencion?->format('Y-m-d H:i:s'),
            'atendido_por' => $interaccion->atendidoPor ? [
                'id' => $interaccion->atendidoPor->id,
                'nombre' => $interaccion->atendidoPor->usuario->persona->nombre_completo ?? null,
            ] : null,
        ];
    }
    
    /**
     * Formatear interacción completa
     */
    private function formatearInteraccionCompleta($interaccion): array
    {
        $data = $this->formatearInteraccion($interaccion);
        $data['foto_url'] = $interaccion->foto_url;
        $data['notas_atencion'] = $interaccion->notas_atencion;
        $data['created_at'] = $interaccion->created_at?->format('Y-m-d H:i:s');
        $data['updated_at'] = $interaccion->updated_at?->format('Y-m-d H:i:s');
        
        return $data;
    }
}
