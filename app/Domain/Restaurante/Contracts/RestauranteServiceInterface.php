<?php

namespace App\Domain\Restaurante\Contracts;

use App\Domain\Restaurante\Models\Restaurante;

interface RestauranteServiceInterface
{
    /**
     * Crear un restaurante
     *
     * @param array $data
     * @return Restaurante
     */
    public function crear(array $data): Restaurante;
    
    /**
     * Actualizar un restaurante
     *
     * @param int $id
     * @param array $data
     * @return Restaurante
     */
    public function actualizar(int $id, array $data): Restaurante;
    
    /**
     * Obtener restaurante por ID con relaciones
     *
     * @param int $id
     * @return Restaurante
     */
    public function obtenerPorId(int $id): Restaurante;
    
    /**
     * Obtener restaurante por slug
     *
     * @param string $slug
     * @return Restaurante
     */
    public function obtenerPorSlug(string $slug): Restaurante;
    
    /**
     * Cambiar estado activo/inactivo
     *
     * @param int $id
     * @param bool $activo
     * @return Restaurante
     */
    public function cambiarEstado(int $id, bool $activo): Restaurante;
    
    /**
     * Verificar restaurante
     *
     * @param int $id
     * @param bool $verificado
     * @return Restaurante
     */
    public function verificar(int $id, bool $verificado): Restaurante;
}
