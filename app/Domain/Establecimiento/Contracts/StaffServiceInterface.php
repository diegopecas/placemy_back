<?php

namespace App\Domain\Establecimiento\Contracts;

interface StaffServiceInterface
{
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
    
    public function asignarAEstablecimiento(int $staffId, int $establecimientoId, int $cargoId, int $usuarioId): array;
    
    public function desasignarDeEstablecimiento(int $staffId, int $establecimientoId): bool;
}
