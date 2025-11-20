<?php

namespace App\Domain\Cliente\Contracts;

interface ClienteEstablecimientoServiceInterface
{
    public function listarPorCliente(int $clienteId): array;
    
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
}
