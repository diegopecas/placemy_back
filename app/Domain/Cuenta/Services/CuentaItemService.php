<?php

namespace App\Domain\Cuenta\Services;

use App\Domain\Cuenta\Contracts\CuentaItemServiceInterface;
use App\Domain\Cuenta\Repositories\CuentaItemRepository;
use App\Domain\Cuenta\Repositories\CuentaRepository;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class CuentaItemService implements CuentaItemServiceInterface
{
    protected $cuentaItemRepository;
    protected $cuentaRepository;
    protected $auditoriaService;
    
    public function __construct(
        CuentaItemRepository $cuentaItemRepository,
        CuentaRepository $cuentaRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->cuentaItemRepository = $cuentaItemRepository;
        $this->cuentaRepository = $cuentaRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar items por cuenta
     */
    public function listarPorCuenta(int $cuentaId): array
    {
        $items = $this->cuentaItemRepository->findByCuenta($cuentaId);
        
        return $items->map(function ($item) {
            return $this->formatearItem($item);
        })->toArray();
    }
    
    /**
     * Obtener item por ID
     */
    public function obtenerPorId(int $id): array
    {
        $item = $this->cuentaItemRepository->findByIdWithRelations($id);
        
        if (!$item) {
            throw new NotFoundException('Item no encontrado');
        }
        
        return $this->formatearItemCompleto($item);
    }
    
    /**
     * Crear item en cuenta
     */
    public function crear(array $data): array
    {
        // Validar que la cuenta existe
        $cuenta = $this->cuentaRepository->findByIdOrFail($data['cuenta_id']);
        
        // Validar que la cuenta esté en estado ABIERTA
        if ($cuenta->estado->codigo !== 'ABIERTA') {
            throw new BusinessException('Solo se pueden agregar items a cuentas abiertas');
        }
        
        // Validar que tenga plato O producto (no ambos, no ninguno)
        if (
            (!isset($data['establecimiento_plato_id']) && !isset($data['establecimiento_producto_id'])) ||
            (isset($data['establecimiento_plato_id']) && isset($data['establecimiento_producto_id']))
        ) {
            throw new BusinessException('Debe proporcionar establecimiento_plato_id O establecimiento_producto_id');
        }
        
        DB::beginTransaction();
        try {
            // Determinar tipo de item
            $tipoItemId = isset($data['establecimiento_plato_id'])
                ? $this->obtenerTipoItemId('PLATO')
                : $this->obtenerTipoItemId('PRODUCTO');
            
            // Calcular subtotal
            $cantidad = $data['cantidad'];
            $precioUnitario = $data['precio_unitario'];
            $descuento = $data['descuento'] ?? 0;
            $subtotal = ($cantidad * $precioUnitario) - $descuento;
            
            // Buscar estado PENDIENTE
            $estadoPendiente = DB::table('cuenta_item_estados')
                ->where('codigo', 'PENDIENTE')
                ->first();
            
            // Preparar datos
            $datosCrear = [
                'cuenta_id' => $data['cuenta_id'],
                'tipo_item_id' => $tipoItemId,
                'establecimiento_plato_id' => $data['establecimiento_plato_id'] ?? null,
                'establecimiento_producto_id' => $data['establecimiento_producto_id'] ?? null,
                'estado_id' => $estadoPendiente->id,
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'descuento' => $descuento,
                'subtotal' => $subtotal,
                'notas_especiales' => $data['notas_especiales'] ?? null,
            ];
            
            // Crear item
            $item = $this->cuentaItemRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_items',
                $item->id,
                'INSERT',
                auth()->id(),
                null,
                $item->toArray()
            );
            
            // Recalcular totales de la cuenta
            $this->recalcularTotalesCuenta($data['cuenta_id']);
            
            DB::commit();
            
            // Recargar con relaciones
            $item = $this->cuentaItemRepository->findByIdWithRelations($item->id);
            return $this->formatearItemCompleto($item);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar item
     */
    public function actualizar(int $id, array $data): array
    {
        $item = $this->cuentaItemRepository->findByIdOrFail($id);
        
        // Validar que el item sea modificable
        if (!$item->estado->permite_modificacion) {
            throw new BusinessException('El item no puede ser modificado en su estado actual');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $item->toArray();
            
            // Preparar datos a actualizar
            $datosActualizar = [];
            
            if (isset($data['cantidad'])) {
                $datosActualizar['cantidad'] = $data['cantidad'];
            }
            
            if (isset($data['notas_especiales'])) {
                $datosActualizar['notas_especiales'] = $data['notas_especiales'];
            }
            
            // Recalcular subtotal si cambió cantidad
            if (isset($data['cantidad'])) {
                $cantidad = $data['cantidad'];
                $subtotal = ($cantidad * $item->precio_unitario) - $item->descuento;
                $datosActualizar['subtotal'] = $subtotal;
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            // Actualizar
            $itemActualizado = $this->cuentaItemRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_items',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $itemActualizado->toArray()
            );
            
            // Recalcular totales de la cuenta
            $this->recalcularTotalesCuenta($item->cuenta_id);
            
            DB::commit();
            
            // Recargar con relaciones
            $itemActualizado = $this->cuentaItemRepository->findByIdWithRelations($id);
            return $this->formatearItemCompleto($itemActualizado);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar estado de item
     */
    public function cambiarEstado(int $id, int $estadoId): array
    {
        $item = $this->cuentaItemRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $item->toArray();
            
            $itemActualizado = $this->cuentaItemRepository->update($id, ['estado_id' => $estadoId]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_items',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $itemActualizado->toArray()
            );
            
            DB::commit();
            
            $itemActualizado = $this->cuentaItemRepository->findByIdWithRelations($id);
            return $this->formatearItemCompleto($itemActualizado);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar item (solo si es modificable)
     */
    public function eliminar(int $id): bool
    {
        $item = $this->cuentaItemRepository->findByIdOrFail($id);
        
        // Validar que el item sea modificable
        if (!$item->estado->permite_modificacion) {
            throw new BusinessException('El item no puede ser eliminado en su estado actual');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $item->toArray();
            $cuentaId = $item->cuenta_id;
            
            // Eliminar item
            $this->cuentaItemRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_items',
                $id,
                'DELETE',
                auth()->id(),
                $datosAnteriores,
                null
            );
            
            // Recalcular totales de la cuenta
            $this->recalcularTotalesCuenta($cuentaId);
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Listar items modificables de una cuenta
     */
    public function listarModificables(int $cuentaId): array
    {
        $items = $this->cuentaItemRepository->findModificablesByCuenta($cuentaId);
        
        return $items->map(function ($item) {
            return $this->formatearItem($item);
        })->toArray();
    }
    
    /**
     * Obtener ID del tipo de item por código
     */
    private function obtenerTipoItemId(string $codigo): int
    {
        $tipo = DB::table('tipos_items')
            ->where('codigo', $codigo)
            ->first();
        
        if (!$tipo) {
            throw new BusinessException("Tipo de item '{$codigo}' no encontrado");
        }
        
        return $tipo->id;
    }
    
    /**
     * Recalcular totales de la cuenta
     */
    private function recalcularTotalesCuenta(int $cuentaId): void
    {
        $subtotal = $this->cuentaItemRepository->calcularSubtotalCuenta($cuentaId);
        
        $cuenta = $this->cuentaRepository->findByIdOrFail($cuentaId);
        $total = $subtotal - $cuenta->descuento + $cuenta->total_impuestos + $cuenta->propina;
        
        $this->cuentaRepository->update($cuentaId, [
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }
    
    /**
     * Formatear item simple
     */
    private function formatearItem($item): array
    {
        return [
            'id' => $item->id,
            'cuenta_id' => $item->cuenta_id,
            'tipo_item' => $item->tipoItem ? [
                'id' => $item->tipoItem->id,
                'codigo' => $item->tipoItem->codigo,
                'nombre' => $item->tipoItem->nombre,
            ] : null,
            'plato' => $item->plato && $item->plato->plato ? [
                'id' => $item->plato->id,
                'nombre' => $item->plato->plato->nombre,
                'precio' => (float) $item->plato->precio,
            ] : null,
            'producto' => $item->producto && $item->producto->producto ? [
                'id' => $item->producto->id,
                'nombre' => $item->producto->producto->nombre,
                'precio' => (float) $item->producto->precio_individual,
            ] : null,
            'estado' => $item->estado ? [
                'id' => $item->estado->id,
                'codigo' => $item->estado->codigo,
                'nombre' => $item->estado->nombre,
                'color' => $item->estado->color,
                'permite_modificacion' => $item->estado->permite_modificacion,
            ] : null,
            'cantidad' => $item->cantidad,
            'precio_unitario' => (float) $item->precio_unitario,
            'descuento' => (float) $item->descuento,
            'subtotal' => (float) $item->subtotal,
            'notas_especiales' => $item->notas_especiales,
            'created_at' => $item->created_at?->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * Formatear item completo
     */
    private function formatearItemCompleto($item): array
    {
        $data = $this->formatearItem($item);
        $data['divisiones_count'] = $item->divisiones ? $item->divisiones->count() : 0;
        $data['updated_at'] = $item->updated_at?->format('Y-m-d H:i:s');
        
        return $data;
    }
}
