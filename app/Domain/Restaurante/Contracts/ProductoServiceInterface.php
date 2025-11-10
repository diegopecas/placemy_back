<?php

namespace App\Domain\Restaurante\Contracts;

use App\Domain\Restaurante\Models\Producto;

interface ProductoServiceInterface
{
    /**
     * Crear un producto
     *
     * @param array $data
     * @return Producto
     */
    public function crear(array $data): Producto;
    
    /**
     * Actualizar un producto
     *
     * @param int $id
     * @param array $data
     * @return Producto
     */
    public function actualizar(int $id, array $data): Producto;
    
    /**
     * Obtener producto por ID con relaciones
     *
     * @param int $id
     * @return Producto
     */
    public function obtenerPorId(int $id): Producto;
    
    /**
     * Asignar producto a restaurante
     *
     * @param int $productoId
     * @param int $restauranteId
     * @param array $data
     * @return void
     */
    public function asignarARestaurante(int $productoId, int $restauranteId, array $data): void;
    
    /**
     * Actualizar producto en restaurante (precio, disponibilidad)
     *
     * @param int $productoId
     * @param int $restauranteId
     * @param array $data
     * @return void
     */
    public function actualizarEnRestaurante(int $productoId, int $restauranteId, array $data): void;
    
    /**
     * Desasignar producto de restaurante
     *
     * @param int $productoId
     * @param int $restauranteId
     * @return void
     */
    public function desasignarDeRestaurante(int $productoId, int $restauranteId): void;
}
