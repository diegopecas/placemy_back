<?php

namespace App\Domain\Establecimiento\Contracts;

interface EstablecimientoServiceInterface
{
    public function listar(array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function obtenerPorSlug(string $slug): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
    
    public function cambiarEstado(int $id, bool $activo): array;
}
