<?php

namespace App\Domain\Establecimiento\Contracts;

interface EstablecimientoStaffServiceInterface
{
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
    
    public function cambiarEstado(int $id, bool $activo): array;
    
    public function obtenerPorCargo(int $establecimientoId, int $cargoId): array;
}