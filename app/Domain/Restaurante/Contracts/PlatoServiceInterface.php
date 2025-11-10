<?php

namespace App\Domain\Restaurante\Contracts;

use App\Domain\Restaurante\Models\Plato;

interface PlatoServiceInterface
{
    /**
     * Crear un plato
     *
     * @param array $data
     * @return Plato
     */
    public function crear(array $data): Plato;
    
    /**
     * Actualizar un plato
     *
     * @param int $id
     * @param array $data
     * @return Plato
     */
    public function actualizar(int $id, array $data): Plato;
    
    /**
     * Obtener plato por ID con relaciones
     *
     * @param int $id
     * @return Plato
     */
    public function obtenerPorId(int $id): Plato;
    
    /**
     * Asignar plato a restaurante
     *
     * @param int $platoId
     * @param int $restauranteId
     * @param array $data
     * @return void
     */
    public function asignarARestaurante(int $platoId, int $restauranteId, array $data): void;
    
    /**
     * Actualizar plato en restaurante (precio, disponibilidad)
     *
     * @param int $platoId
     * @param int $restauranteId
     * @param array $data
     * @return void
     */
    public function actualizarEnRestaurante(int $platoId, int $restauranteId, array $data): void;
    
    /**
     * Desasignar plato de restaurante
     *
     * @param int $platoId
     * @param int $restauranteId
     * @return void
     */
    public function desasignarDeRestaurante(int $platoId, int $restauranteId): void;
}
