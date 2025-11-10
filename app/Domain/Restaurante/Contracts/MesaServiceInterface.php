<?php

namespace App\Domain\Restaurante\Contracts;

use App\Domain\Restaurante\Models\Mesa;

interface MesaServiceInterface
{
    /**
     * Crear una mesa
     *
     * @param array $data
     * @return Mesa
     */
    public function crear(array $data): Mesa;
    
    /**
     * Actualizar una mesa
     *
     * @param int $id
     * @param array $data
     * @return Mesa
     */
    public function actualizar(int $id, array $data): Mesa;
    
    /**
     * Cambiar estado de la mesa
     *
     * @param int $id
     * @param int $estadoId
     * @return Mesa
     */
    public function cambiarEstado(int $id, int $estadoId): Mesa;
    
    /**
     * Asignar staff a mesa
     *
     * @param int $id
     * @param int|null $staffId
     * @return Mesa
     */
    public function asignarStaff(int $id, ?int $staffId): Mesa;
    
    /**
     * Obtener mesa por ID con relaciones
     *
     * @param int $id
     * @return Mesa
     */
    public function obtenerPorId(int $id): Mesa;
}
