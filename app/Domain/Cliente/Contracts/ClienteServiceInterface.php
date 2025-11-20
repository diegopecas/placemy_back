<?php

namespace App\Domain\Cliente\Contracts;

interface ClienteServiceInterface
{
    public function listar(array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function crear(array $datos): array;
    
    public function crearCompleto(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function eliminar(int $id): bool;
}
