<?php

namespace App\Domain\Cuenta\Services;

use App\Domain\Cuenta\Contracts\CuentaServiceInterface;
use App\Domain\Cuenta\Repositories\CuentaRepository;
use App\Domain\Cuenta\Repositories\CuentaItemRepository;
use App\Domain\Cuenta\Repositories\CuentaImpuestoRepository;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CuentaService implements CuentaServiceInterface
{
    protected $cuentaRepository;
    protected $cuentaItemRepository;
    protected $cuentaImpuestoRepository;
    protected $auditoriaService;
    
    public function __construct(
        CuentaRepository $cuentaRepository,
        CuentaItemRepository $cuentaItemRepository,
        CuentaImpuestoRepository $cuentaImpuestoRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->cuentaRepository = $cuentaRepository;
        $this->cuentaItemRepository = $cuentaItemRepository;
        $this->cuentaImpuestoRepository = $cuentaImpuestoRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar cuentas con filtros
     */
    public function listar(array $filtros = []): array
    {
        // Validar que se proporcione establecimiento_id
        if (!isset($filtros['establecimiento_id'])) {
            throw new BusinessException('El establecimiento es obligatorio');
        }
        
        $cuentas = $this->cuentaRepository->findByEstablecimiento($filtros['establecimiento_id'], $filtros);
        
        return $cuentas->map(function ($cuenta) {
            return $this->formatearCuenta($cuenta);
        })->toArray();
    }
    
    /**
     * Obtener cuenta por ID
     */
    public function obtenerPorId(int $id): array
    {
        $cuenta = $this->cuentaRepository->findByIdWithRelations($id);
        
        if (!$cuenta) {
            throw new NotFoundException('Cuenta no encontrada');
        }
        
        return $this->formatearCuentaCompleta($cuenta);
    }
    
    /**
     * Obtener cuenta por número
     */
    public function obtenerPorNumeroCuenta(string $numeroCuenta): array
    {
        $cuenta = $this->cuentaRepository->findByNumeroCuenta($numeroCuenta);
        
        if (!$cuenta) {
            throw new NotFoundException('Cuenta no encontrada');
        }
        
        $cuenta = $this->cuentaRepository->findByIdWithRelations($cuenta->id);
        return $this->formatearCuentaCompleta($cuenta);
    }
    
    /**
     * Obtener cuenta por palabra secreta
     */
    public function obtenerPorPalabraSecreta(string $palabraSecreta): array
    {
        $cuenta = $this->cuentaRepository->findByPalabraSecreta($palabraSecreta);
        
        if (!$cuenta) {
            throw new NotFoundException('Cuenta no encontrada');
        }
        
        $cuenta = $this->cuentaRepository->findByIdWithRelations($cuenta->id);
        return $this->formatearCuentaCompleta($cuenta);
    }
    
    /**
     * Crear cuenta
     */
    public function crear(array $data): array
    {
        // Validar que la mesa no tenga cuenta activa
        $cuentaActiva = $this->cuentaRepository->findCuentaActivaMesa($data['mesa_id']);
        if ($cuentaActiva) {
            throw new BusinessException('La mesa ya tiene una cuenta activa');
        }
        
        // Generar número de cuenta
        $numeroCuenta = $this->cuentaRepository->generarNumeroCuenta($data['establecimiento_id']);
        
        // Generar palabra secreta
        $palabraSecreta = $this->generarPalabraSecreta();
        
        DB::beginTransaction();
        try {
            // Preparar datos
            $datosCrear = [
                'numero_cuenta' => $numeroCuenta,
                'establecimiento_id' => $data['establecimiento_id'],
                'mesa_id' => $data['mesa_id'],
                'establecimiento_staff_id' => $data['establecimiento_staff_id'],
                'cliente_id' => $data['cliente_id'] ?? null,
                'estado_id' => $data['estado_id'],
                'palabra_secreta' => $palabraSecreta,
                'incluir_impuestos' => $data['incluir_impuestos'] ?? true,
                'subtotal' => 0,
                'descuento' => 0,
                'total_impuestos' => 0,
                'propina' => 0,
                'propina_porcentaje' => null,
                'total' => 0,
                'fecha_apertura' => now(),
                'notas_cliente' => $data['notas_cliente'] ?? null,
                'notas_internas' => $data['notas_internas'] ?? null,
                'activo' => true,
            ];
            
            // Crear cuenta
            $cuenta = $this->cuentaRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuentas',
                $cuenta->id,
                'INSERT',
                auth()->id(),
                null,
                $cuenta->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $cuenta = $this->cuentaRepository->findByIdWithRelations($cuenta->id);
            return $this->formatearCuentaCompleta($cuenta);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar cuenta
     */
    public function actualizar(int $id, array $data): array
    {
        $cuenta = $this->cuentaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $cuenta->toArray();
            
            // Preparar datos a actualizar
            $datosActualizar = [];
            
            if (isset($data['cliente_id'])) {
                $datosActualizar['cliente_id'] = $data['cliente_id'];
            }
            
            if (isset($data['descuento'])) {
                $datosActualizar['descuento'] = $data['descuento'];
            }
            
            if (isset($data['propina'])) {
                $datosActualizar['propina'] = $data['propina'];
                $datosActualizar['propina_porcentaje'] = $data['propina_porcentaje'] ?? null;
            }
            
            if (isset($data['notas_cliente'])) {
                $datosActualizar['notas_cliente'] = $data['notas_cliente'];
            }
            
            if (isset($data['notas_internas'])) {
                $datosActualizar['notas_internas'] = $data['notas_internas'];
            }
            
            if (empty($datosActualizar)) {
                throw new BusinessException('No hay datos para actualizar');
            }
            
            // Actualizar
            $cuentaActualizada = $this->cuentaRepository->update($id, $datosActualizar);
            
            // Recalcular totales si se modificó descuento o propina
            if (isset($data['descuento']) || isset($data['propina'])) {
                $this->recalcularTotales($id);
            }
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuentas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $cuentaActualizada->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $cuentaActualizada = $this->cuentaRepository->findByIdWithRelations($id);
            return $this->formatearCuentaCompleta($cuentaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cambiar estado de cuenta
     */
    public function cambiarEstado(int $id, int $estadoId): array
    {
        $cuenta = $this->cuentaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $cuenta->toArray();
            
            $cuentaActualizada = $this->cuentaRepository->update($id, ['estado_id' => $estadoId]);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuentas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $cuentaActualizada->toArray()
            );
            
            DB::commit();
            
            $cuentaActualizada = $this->cuentaRepository->findByIdWithRelations($id);
            return $this->formatearCuentaCompleta($cuentaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Cerrar cuenta
     */
    public function cerrarCuenta(int $id, int $staffId): array
    {
        $cuenta = $this->cuentaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $cuenta->toArray();
            
            $datosActualizar = [
                'cerrada_por_staff_id' => $staffId,
                'fecha_cierre' => now(),
            ];
            
            // Buscar estado CERRADA
            $estadoCerrada = DB::table('cuenta_estados')
                ->where('codigo', 'CERRADA')
                ->first();
            
            if ($estadoCerrada) {
                $datosActualizar['estado_id'] = $estadoCerrada->id;
            }
            
            $cuentaActualizada = $this->cuentaRepository->update($id, $datosActualizar);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuentas',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $cuentaActualizada->toArray()
            );
            
            DB::commit();
            
            $cuentaActualizada = $this->cuentaRepository->findByIdWithRelations($id);
            return $this->formatearCuentaCompleta($cuentaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Calcular totales de cuenta
     */
    public function calcularTotales(int $id): array
    {
        $cuenta = $this->cuentaRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $this->recalcularTotales($id);
            
            DB::commit();
            
            $cuentaActualizada = $this->cuentaRepository->findByIdWithRelations($id);
            return $this->formatearCuentaCompleta($cuentaActualizada);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener cuenta activa de una mesa
     */
    public function obtenerCuentaActivaMesa(int $mesaId): ?array
    {
        $cuenta = $this->cuentaRepository->findCuentaActivaMesa($mesaId);
        
        if (!$cuenta) {
            return null;
        }
        
        $cuenta = $this->cuentaRepository->findByIdWithRelations($cuenta->id);
        return $this->formatearCuentaCompleta($cuenta);
    }
    
    /**
     * Recalcular totales de la cuenta
     */
    private function recalcularTotales(int $cuentaId): void
    {
        // Calcular subtotal de items
        $subtotal = $this->cuentaItemRepository->calcularSubtotalCuenta($cuentaId);
        
        // Calcular impuestos
        $totalImpuestos = $this->cuentaImpuestoRepository->calcularTotalImpuestosCuenta($cuentaId);
        
        // Obtener cuenta para descuento y propina
        $cuenta = $this->cuentaRepository->findByIdOrFail($cuentaId);
        
        // Calcular total
        $total = $subtotal - $cuenta->descuento + $totalImpuestos + $cuenta->propina;
        
        // Actualizar cuenta
        $this->cuentaRepository->update($cuentaId, [
            'subtotal' => $subtotal,
            'total_impuestos' => $totalImpuestos,
            'total' => $total,
        ]);
    }
    
    /**
     * Generar palabra secreta única
     */
    private function generarPalabraSecreta(): string
    {
        $palabras = ['mesa', 'cuenta', 'chef', 'sabor', 'plato', 'vino', 'postre', 'cafe'];
        $numero = rand(100, 999);
        
        do {
            $palabra = $palabras[array_rand($palabras)] . $numero;
            $existe = $this->cuentaRepository->findByPalabraSecreta($palabra);
        } while ($existe);
        
        return $palabra;
    }
    
    /**
     * Formatear cuenta simple
     */
    private function formatearCuenta($cuenta): array
    {
        return [
            'id' => $cuenta->id,
            'numero_cuenta' => $cuenta->numero_cuenta,
            'establecimiento_id' => $cuenta->establecimiento_id,
            'mesa' => $cuenta->mesa ? [
                'id' => $cuenta->mesa->id,
                'identificacion_mesa' => $cuenta->mesa->identificacion_mesa,
            ] : null,
            'mesero' => $cuenta->mesero ? [
                'id' => $cuenta->mesero->id,
                'codigo_empleado' => $cuenta->mesero->codigo_empleado,
            ] : null,
            'cliente_id' => $cuenta->cliente_id,
            'estado' => $cuenta->estado ? [
                'id' => $cuenta->estado->id,
                'codigo' => $cuenta->estado->codigo,
                'nombre' => $cuenta->estado->nombre,
                'color' => $cuenta->estado->color,
            ] : null,
            'subtotal' => (float) $cuenta->subtotal,
            'total' => (float) $cuenta->total,
            'fecha_apertura' => $cuenta->fecha_apertura?->format('Y-m-d H:i:s'),
            'fecha_cierre' => $cuenta->fecha_cierre?->format('Y-m-d H:i:s'),
            'activo' => $cuenta->activo,
        ];
    }
    
    /**
     * Formatear cuenta completa con todas las relaciones
     */
    private function formatearCuentaCompleta($cuenta): array
    {
        return [
            'id' => $cuenta->id,
            'numero_cuenta' => $cuenta->numero_cuenta,
            'palabra_secreta' => $cuenta->palabra_secreta,
            'establecimiento_id' => $cuenta->establecimiento_id,
            'mesa' => $cuenta->mesa ? [
                'id' => $cuenta->mesa->id,
                'identificacion_mesa' => $cuenta->mesa->identificacion_mesa,
            ] : null,
            'mesero' => $cuenta->mesero ? [
                'id' => $cuenta->mesero->id,
                'codigo_empleado' => $cuenta->mesero->codigo_empleado,
                'nombre' => $cuenta->mesero->usuario->persona->nombre_completo ?? null,
            ] : null,
            'cerrado_por' => $cuenta->cerradoPor ? [
                'id' => $cuenta->cerradoPor->id,
                'codigo_empleado' => $cuenta->cerradoPor->codigo_empleado,
                'nombre' => $cuenta->cerradoPor->usuario->persona->nombre_completo ?? null,
            ] : null,
            'cliente' => $cuenta->cliente ? [
                'id' => $cuenta->cliente->id,
                'nombre' => $cuenta->cliente->nombre_completo,
            ] : null,
            'estado' => $cuenta->estado ? [
                'id' => $cuenta->estado->id,
                'codigo' => $cuenta->estado->codigo,
                'nombre' => $cuenta->estado->nombre,
                'color' => $cuenta->estado->color,
                'icono' => $cuenta->estado->icono,
            ] : null,
            'incluir_impuestos' => $cuenta->incluir_impuestos,
            'subtotal' => (float) $cuenta->subtotal,
            'descuento' => (float) $cuenta->descuento,
            'total_impuestos' => (float) $cuenta->total_impuestos,
            'propina' => (float) $cuenta->propina,
            'propina_porcentaje' => $cuenta->propina_porcentaje ? (float) $cuenta->propina_porcentaje : null,
            'total' => (float) $cuenta->total,
            'fecha_apertura' => $cuenta->fecha_apertura?->format('Y-m-d H:i:s'),
            'fecha_cierre' => $cuenta->fecha_cierre?->format('Y-m-d H:i:s'),
            'notas_cliente' => $cuenta->notas_cliente,
            'notas_internas' => $cuenta->notas_internas,
            'activo' => $cuenta->activo,
            'items_count' => $cuenta->items ? $cuenta->items->count() : 0,
            'divisiones_count' => $cuenta->divisiones ? $cuenta->divisiones->count() : 0,
            'pagos_count' => $cuenta->pagos ? $cuenta->pagos->count() : 0,
            'interacciones_count' => $cuenta->interacciones ? $cuenta->interacciones->count() : 0,
            'created_at' => $cuenta->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $cuenta->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
