<?php

namespace App\Domain\Cuenta\Contracts;

interface CuentaDivisionServiceInterface
{
    public function listarPorCuenta(int $cuentaId): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
    
    public function asignarItems(int $divisionId, array $items): array;
    
    public function marcarComoPagado(int $id): array;
}
