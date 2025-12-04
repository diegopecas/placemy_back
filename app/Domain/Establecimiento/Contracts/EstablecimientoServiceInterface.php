<?php

namespace App\Domain\Establecimiento\Contracts;

use App\Domain\Establecimiento\Models\Establecimiento;
use Illuminate\Database\Eloquent\Collection;

interface EstablecimientoServiceInterface
{
    /**
     * Listar establecimientos con filtros
     */
    public function listar(array $filtros = []): array;
    
    /**
     * Obtener establecimiento por ID
     * ✅ ACTUALIZADO: Retorna Establecimiento en vez de array
     */
    public function obtenerPorId(int $id): Establecimiento;
    
    /**
     * Obtener establecimiento por slug
     * ✅ ACTUALIZADO: Retorna Establecimiento en vez de array
     */
    public function obtenerPorSlug(string $slug): Establecimiento;
    
    /**
     * Crear establecimiento
     * ✅ ACTUALIZADO: Retorna Establecimiento en vez de array
     */
    public function crear(array $datos): Establecimiento;
    
    /**
     * Actualizar establecimiento
     * ✅ ACTUALIZADO: Retorna Establecimiento en vez de array
     */
    public function actualizar(int $id, array $datos): Establecimiento;
    
    /**
     * Eliminar establecimiento
     */
    public function eliminar(int $id): bool;
    
    /**
     * Cambiar estado del establecimiento
     * ✅ ACTUALIZADO: Retorna Establecimiento en vez de array
     */
    public function cambiarEstado(int $id, bool $activo): Establecimiento;
    
    /**
     * ✅ NUEVO: Obtener todos los establecimientos
     */
    public function obtenerTodos(): Collection;
    
    /**
     * ✅ NUEVO: Obtener menú completo del establecimiento
     */
    public function obtenerMenu(int $establecimientoId): array;
    
    /**
     * ✅ NUEVO: Verificar establecimiento
     */
    public function verificar(int $id, bool $verificado): Establecimiento;
}