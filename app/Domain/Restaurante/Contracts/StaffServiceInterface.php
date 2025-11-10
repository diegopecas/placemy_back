<?php

namespace App\Domain\Restaurante\Contracts;

use App\Domain\Restaurante\Models\Staff;

interface StaffServiceInterface
{
    /**
     * Crear staff
     *
     * @param array $data
     * @return Staff
     */
    public function crear(array $data): Staff;
    
    /**
     * Actualizar staff
     *
     * @param int $id
     * @param array $data
     * @return Staff
     */
    public function actualizar(int $id, array $data): Staff;
    
    /**
     * Obtener staff por ID con relaciones
     *
     * @param int $id
     * @return Staff
     */
    public function obtenerPorId(int $id): Staff;
    
    /**
     * Asignar staff a restaurante
     *
     * @param int $staffId
     * @param int $restauranteId
     * @param array $data
     * @return void
     */
    public function asignarARestaurante(int $staffId, int $restauranteId, array $data): void;
    
    /**
     * Actualizar asignación de staff en restaurante
     *
     * @param int $staffId
     * @param int $restauranteId
     * @param array $data
     * @return void
     */
    public function actualizarEnRestaurante(int $staffId, int $restauranteId, array $data): void;
    
    /**
     * Desasignar staff de restaurante
     *
     * @param int $staffId
     * @param int $restauranteId
     * @return void
     */
    public function desasignarDeRestaurante(int $staffId, int $restauranteId): void;
    
    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id
     * @param bool $activo
     * @return Staff
     */
    public function cambiarEstado(int $id, bool $activo): Staff;
}
