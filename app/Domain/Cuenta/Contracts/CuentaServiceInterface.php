<?php

namespace App\Domain\Cuenta\Contracts;

interface CuentaServiceInterface
{
    public function listar(array $filtros = []): array;
    
    public function obtenerPorId(int $id): array;
    
    public function obtenerPorNumeroCuenta(string $numeroCuenta): array;
    
    public function obtenerPorPalabraSecreta(string $palabraSecreta): array;
    
    public function crear(array $datos): array;
    
    public function actualizar(int $id, array $datos): array;
    
    public function cambiarEstado(int $id, int $estadoId): array;
    
    public function cerrarCuenta(int $id, int $staffId): array;
    
    public function calcularTotales(int $id): array;
    
    public function obtenerCuentaActivaMesa(int $mesaId): ?array;
}
