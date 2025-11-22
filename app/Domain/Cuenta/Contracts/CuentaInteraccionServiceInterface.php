<?php

namespace App\Domain\Cuenta\Contracts;

interface CuentaInteraccionServiceInterface
{
    public function listarPorCuenta(int $cuentaId): array;
    
    public function listarPendientesPorEstablecimiento(int $establecimientoId): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function atender(int $id, int $staffId, string $notas = null): array;
    
    public function cambiarEstado(int $id, int $estadoId): array;
}
