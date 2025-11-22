<?php

namespace App\Domain\Cuenta\Services;

use App\Domain\Cuenta\Contracts\CuentaPagoServiceInterface;
use App\Domain\Cuenta\Repositories\CuentaPagoRepository;
use App\Domain\Cuenta\Repositories\CuentaRepository;
use App\Domain\Cuenta\Repositories\CuentaDivisionRepository;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class CuentaPagoService implements CuentaPagoServiceInterface
{
    protected $pagoRepository;
    protected $cuentaRepository;
    protected $divisionRepository;
    protected $auditoriaService;
    
    public function __construct(
        CuentaPagoRepository $pagoRepository,
        CuentaRepository $cuentaRepository,
        CuentaDivisionRepository $divisionRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->pagoRepository = $pagoRepository;
        $this->cuentaRepository = $cuentaRepository;
        $this->divisionRepository = $divisionRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar pagos por cuenta
     */
    public function listarPorCuenta(int $cuentaId): array
    {
        $pagos = $this->pagoRepository->findByCuenta($cuentaId);
        
        return $pagos->map(function ($pago) {
            return $this->formatearPago($pago);
        })->toArray();
    }
    
    /**
     * Obtener pago por ID
     */
    public function obtenerPorId(int $id): array
    {
        $pago = $this->pagoRepository->findByIdWithRelations($id);
        
        if (!$pago) {
            throw new NotFoundException('Pago no encontrado');
        }
        
        return $this->formatearPagoCompleto($pago);
    }
    
    /**
     * Registrar pago de cuenta
     */
    public function registrarPago(array $data): array
    {
        // Validar que la cuenta existe
        $cuenta = $this->cuentaRepository->findByIdOrFail($data['cuenta_id']);
        
        // Si es pago de división, validar que existe
        if (isset($data['cuenta_division_id'])) {
            $division = $this->divisionRepository->findByIdOrFail($data['cuenta_division_id']);
            
            // Validar que el monto no exceda el total asignado
            $totalPagado = $this->pagoRepository->calcularTotalPagadoDivision($data['cuenta_division_id']);
            $nuevoTotal = $totalPagado + $data['monto'];
            
            if ($nuevoTotal > $division->total_asignado) {
                throw new BusinessException('El monto total pagado excede el total asignado a esta persona');
            }
        } else {
            // Validar que el monto no exceda el total de la cuenta
            $totalPagado = $this->pagoRepository->calcularTotalPagado($data['cuenta_id']);
            $nuevoTotal = $totalPagado + $data['monto'];
            
            if ($nuevoTotal > $cuenta->total) {
                throw new BusinessException('El monto total pagado excede el total de la cuenta');
            }
        }
        
        DB::beginTransaction();
        try {
            // Preparar datos
            $datosCrear = [
                'cuenta_id' => $data['cuenta_id'],
                'cuenta_division_id' => $data['cuenta_division_id'] ?? null,
                'metodo_pago_id' => $data['metodo_pago_id'],
                'monto' => $data['monto'],
                'fecha_pago' => $data['fecha_pago'] ?? now(),
                'referencia' => $data['referencia'] ?? null,
                'url_soporte' => $data['url_soporte'] ?? null,
                'notas' => $data['notas'] ?? null,
            ];
            
            // Crear pago
            $pago = $this->pagoRepository->create($datosCrear);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'cuenta_pagos',
                $pago->id,
                'INSERT',
                auth()->id(),
                null,
                $pago->toArray()
            );
            
            // Si es pago de división, verificar si está completamente pagada
            if (isset($data['cuenta_division_id'])) {
                $totalPagado = $this->pagoRepository->calcularTotalPagadoDivision($data['cuenta_division_id']);
                
                if ($totalPagado >= $division->total_asignado) {
                    // Marcar división como pagada
                    $this->divisionRepository->update($data['cuenta_division_id'], ['pagado' => true]);
                }
            }
            
            // Verificar si la cuenta está completamente pagada
            $totalPagadoCuenta = $this->pagoRepository->calcularTotalPagado($data['cuenta_id']);
            
            if ($totalPagadoCuenta >= $cuenta->total) {
                // Cambiar estado a PAGADA
                $estadoPagada = DB::table('cuenta_estados')
                    ->where('codigo', 'PAGADA')
                    ->first();
                
                if ($estadoPagada) {
                    $this->cuentaRepository->update($data['cuenta_id'], ['estado_id' => $estadoPagada->id]);
                }
            }
            
            DB::commit();
            
            // Recargar con relaciones
            $pago = $this->pagoRepository->findByIdWithRelations($pago->id);
            return $this->formatearPagoCompleto($pago);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Obtener total pagado de una cuenta
     */
    public function obtenerTotalPagado(int $cuentaId): float
    {
        return $this->pagoRepository->calcularTotalPagado($cuentaId);
    }
    
    /**
     * Formatear pago simple
     */
    private function formatearPago($pago): array
    {
        return [
            'id' => $pago->id,
            'cuenta_id' => $pago->cuenta_id,
            'division' => $pago->division ? [
                'id' => $pago->division->id,
                'nombre' => $pago->division->nombre,
            ] : null,
            'metodo_pago' => $pago->metodoPago ? [
                'id' => $pago->metodoPago->id,
                'nombre' => $pago->metodoPago->nombre,
            ] : null,
            'monto' => (float) $pago->monto,
            'fecha_pago' => $pago->fecha_pago?->format('Y-m-d H:i:s'),
            'referencia' => $pago->referencia,
            'created_at' => $pago->created_at?->format('Y-m-d H:i:s'),
        ];
    }
    
    /**
     * Formatear pago completo
     */
    private function formatearPagoCompleto($pago): array
    {
        $data = $this->formatearPago($pago);
        $data['url_soporte'] = $pago->url_soporte;
        $data['notas'] = $pago->notas;
        $data['updated_at'] = $pago->updated_at?->format('Y-m-d H:i:s');
        
        return $data;
    }
}
