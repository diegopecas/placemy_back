<?php

namespace App\Domain\Cliente\Contracts;

interface ClienteServiceInterface
{
    /**
     * Listar clientes con filtros
     */
    public function listar(array $filtros = []): array;
    
    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId(int $id): array;
    
    /**
     * Crear cliente básico (sin relaciones)
     * Busca o crea PersonaNatural automáticamente
     */
    public function crear(array $data): array;
    
    /**
     * Crear cliente completo (orquestador)
     * Crea: Cliente + Alérgenos + Fechas Especiales
     */
    public function crearCompleto(array $data): array;
    
    /**
     * Actualizar cliente básico (sin relaciones)
     * Puede actualizar datos de PersonaNatural si vienen
     */
    public function actualizar(int $id, array $data): array;
    
    /**
     * Actualizar cliente completo (orquestador)
     * Actualiza: Cliente + PersonaNatural + Sync Alérgenos + Fechas Especiales
     */
    public function actualizarCompleto(int $id, array $data): array;
    
    /**
     * Eliminar cliente
     */
    public function eliminar(int $id): bool;
}
