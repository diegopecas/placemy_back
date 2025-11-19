<?php

namespace App\Domain\Establecimiento\Contracts;

interface ProductoServiceInterface
{
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
    
    public function cambiarDisponibilidad(int $establecimientoId, int $productoId, bool $disponible): array;
}
