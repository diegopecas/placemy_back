<?php

namespace App\Domain\Cuenta\Contracts;

interface CuentaPagoServiceInterface
{
    public function listarPorCuenta(int $cuentaId): array;
    
    public function obtenerPorId(int $id): array;
    
    public function registrarPago(array $datos): array;
    
    public function obtenerTotalPagado(int $cuentaId): float;
}
