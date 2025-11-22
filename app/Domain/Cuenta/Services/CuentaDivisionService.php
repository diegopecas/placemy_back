<?php

namespace App\Domain\Cuenta\Services;

use App\Domain\Cuenta\Contracts\CuentaDivisionServiceInterface;
use App\Domain\Cuenta\Repositories\CuentaDivisionRepository;
use App\Domain\Cuenta\Repositories\CuentaItemDivisionRepository;
use App\Domain\Cuenta\Repositories\CuentaRepository;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class CuentaDivisionService implements CuentaDivisionServiceInterface
{
    protected $divisionRepository;
    protected $itemDivisionRepository;
    protected $cuentaRepository;
    protected $auditoriaService;
    
    public function __construct(
        CuentaDivisionRepository $divisionRepository,
        CuentaItemDivisionRepository $itemDivisionRepository,
        CuentaRepository $cuentaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->divisionRepository = $divisionRepository;
        $this->itemDivisionRepository = $itemDivisionRepository;
        $this->cuentaRepository = $cuentaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar divisiones por cuenta
     */
    public function listarPorCuenta(int $cuentaId): array
    {
        $divisiones = $this->divisionRepository->findByCuenta($cuentaId);
        
        return $divisiones->map(function ($division) {
            return $this->formatearDivision($division);
        })->toArray();
    }
    
    /**
     * Obtener división por ID
     */
    public function obtenerPorId(int $id): array
    {
        $division = $this->divisionRepository->findByIdWithRelations($id);
        
        if (!$division) {
            throw new NotFoundException('División no encontrada');
        }
        
        return $this->formatearDivisionCompleta($division);
    }
    
    /**
     * Crear división de cuenta
     */
    public function crear(array $data): array
    {
        // Validar que la cuenta existe
        $this->cuentaRepository->findByIdOrFail($data['cuenta_id']);
        
        DB::beginTransaction();
        try {
            // Preparar datos
            $datosCrear = [
                'cuenta_id' => $data['cuenta_id'],
                'nombre' => $data['nombre'],
                'subtotal_asignado' => 0,
                'impuestos_asignado' => 0,
                'propina_asignado' => 0,
                'total_asignado' => 0,
                'pagado' => false,
            ];
            
            // Crear división
            $division = $this->divisionRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_divisiones',
                $division->id,
                'INSERT',
                auth()->id(),
                null,
                $division->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $division = $this->divisionRepository->findByIdWithRelations($division->id);
            return $this->formatearDivisionCompleta($division);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar división
     */
    public function actualizar(int $id, array $data): array
    {
        $division = $this->divisionRepository->findByIdOrFail($id);
        
        // Validar que no esté pagada
        if ($division->pagado) {
            throw new BusinessException('No se puede actualizar una división que ya está pagada');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $division->toArray();
            
            // Preparar datos a actualizar
            $datosActualizar = [];
            
            if (isset($data['nombre'])) {
                $datosActualizar['nombre'] = $data['nombre'];
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            // Actualizar
            $divisionActualizada = $this->divisionRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_divisiones',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $divisionActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $divisionActualizada = $this->divisionRepository->findByIdWithRelations($id);
            return $this->formatearDivisionCompleta($divisionActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar división
     */
    public function eliminar(int $id): bool
    {
        $division = $this->divisionRepository->findByIdOrFail($id);
        
        // Validar que no esté pagada
        if ($division->pagado) {
            throw new BusinessException('No se puede eliminar una división que ya está pagada');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $division->toArray();
            
            // Eliminar asignaciones de items
            $this->itemDivisionRepository->deleteByDivision($id);
            
            // Eliminar división
            $this->divisionRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_divisiones',
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
     * Asignar items a una división
     * 
     * @param int $divisionId
     * @param array $items [['cuenta_item_id' => 1, 'porcentaje_asignado' => 100], ...]
     */
    public function asignarItems(int $divisionId, array $items): array
    {
        $division = $this->divisionRepository->findByIdOrFail($divisionId);
        
        // Validar que no esté pagada
        if ($division->pagado) {
            throw new BusinessException('No se pueden asignar items a una división que ya está pagada');
        }
        
        DB::beginTransaction();
        try {
            // Eliminar asignaciones previas
            $this->itemDivisionRepository->deleteByDivision($divisionId);
            
            $subtotalAsignado = 0;
            
            foreach ($items as $itemData) {
                // Obtener el item de cuenta
                $cuentaItem = DB::table('cuenta_items')
                    ->where('id', $itemData['cuenta_item_id'])
                    ->first();
                
                if (!$cuentaItem) {
                    throw new NotFoundException("Item de cuenta {$itemData['cuenta_item_id']} no encontrado");
                }
                
                // Calcular monto asignado
                $porcentaje = $itemData['porcentaje_asignado'];
                $montoAsignado = ($cuentaItem->subtotal * $porcentaje) / 100;
                
                // Crear asignación
                $asignacion = $this->itemDivisionRepository->create([
                    'cuenta_item_id' => $itemData['cuenta_item_id'],
                    'cuenta_division_id' => $divisionId,
                    'porcentaje_asignado' => $porcentaje,
                    'monto_asignado' => $montoAsignado,
                ]);
                
                $subtotalAsignado += $montoAsignado;
                
                // Auditoría
                $this->auditoriaService->registrar(
                    'cuenta_item_divisiones',
                    $asignacion->id,
                    'INSERT',
                    auth()->id(),
                    null,
                    $asignacion->toArray()
                );
            }
            
            // Calcular impuestos y propina proporcional
            $cuenta = $this->cuentaRepository->findByIdOrFail($division->cuenta_id);
            
            $proporcion = $cuenta->subtotal > 0 ? $subtotalAsignado / $cuenta->subtotal : 0;
            $impuestosAsignado = $cuenta->total_impuestos * $proporcion;
            $propinaAsignado = $cuenta->propina * $proporcion;
            $totalAsignado = $subtotalAsignado + $impuestosAsignado + $propinaAsignado;
            
            // Actualizar división
            $this->divisionRepository->update($divisionId, [
                'subtotal_asignado' => $subtotalAsignado,
                'impuestos_asignado' => $impuestosAsignado,
                'propina_asignado' => $propinaAsignado,
                'total_asignado' => $totalAsignado,
            ]);
            
            DB::commit();
            
            // Recargar con relaciones
            $divisionActualizada = $this->divisionRepository->findByIdWithRelations($divisionId);
            return $this->formatearDivisionCompleta($divisionActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Marcar división como pagada
     */
    public function marcarComoPagado(int $id): array
    {
        $division = $this->divisionRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $division->toArray();
            
            $divisionActualizada = $this->divisionRepository->update($id, ['pagado' => true]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_divisiones',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $divisionActualizada->toArray()
            );
            
            DB::commit();
            
            $divisionActualizada = $this->divisionRepository->findByIdWithRelations($id);
            return $this->formatearDivisionCompleta($divisionActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Formatear división simple
     */
    private function formatearDivision($division): array
    {
        return [
            'id' => $division->id,
            'cuenta_id' => $division->cuenta_id,
            'nombre' => $division->nombre,
            'subtotal_asignado' => (float) $division->subtotal_asignado,
            'impuestos_asignado' => (float) $division->impuestos_asignado,
            'propina_asignado' => (float) $division->propina_asignado,
            'total_asignado' => (float) $division->total_asignado,
            'pagado' => $division->pagado,
            'items_count' => $division->itemsAsignados ? $division->itemsAsignados->count() : 0,
            'pagos_count' => $division->pagos ? $division->pagos->count() : 0,
        ];
    }
    
    /**
     * Formatear división completa
     */
    private function formatearDivisionCompleta($division): array
    {
        $data = $this->formatearDivision($division);
        
        $data['items_asignados'] = $division->itemsAsignados ? $division->itemsAsignados->map(function($asignacion) {
            return [
                'id' => $asignacion->id,
                'cuenta_item_id' => $asignacion->cuenta_item_id,
                'porcentaje_asignado' => (float) $asignacion->porcentaje_asignado,
                'monto_asignado' => (float) $asignacion->monto_asignado,
                'item' => $asignacion->item ? [
                    'nombre' => $asignacion->item->plato ? $asignacion->item->plato->plato->nombre : 
                               ($asignacion->item->producto ? $asignacion->item->producto->producto->nombre : 'N/A'),
                    'cantidad' => $asignacion->item->cantidad,
                    'subtotal' => (float) $asignacion->item->subtotal,
                ] : null,
            ];
        })->toArray() : [];
        
        $data['created_at'] = $division->created_at?->format('Y-m-d H:i:s');
        $data['updated_at'] = $division->updated_at?->format('Y-m-d H:i:s');
        
        return $data;
    }
}
