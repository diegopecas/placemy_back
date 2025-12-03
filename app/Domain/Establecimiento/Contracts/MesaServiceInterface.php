<?php

namespace App\Domain\Establecimiento\Contracts;

use App\Domain\Establecimiento\Models\Mesa;
use Illuminate\Database\Eloquent\Collection;

interface MesaServiceInterface
{
    /**
     * Listar mesas por establecimiento
     * 
     * @param int $establecimientoId
     * @param array $filtros
     * @return Collection<Mesa>
     */
    public function listarPorEstablecimiento(int $establecimientoId, array $filtros = []): Collection;
    
    /**
     * Obtener mesa por ID
     * 
     * @param int $id
     * @return Mesa
     * @throws \App\Domain\Shared\Exceptions\BusinessException
     */
    public function obtenerPorId(int $id): Mesa;
    
    /**
     * Crear nueva mesa
     * 
     * @param array $datos
     * @return Mesa
     */
    public function crear(array $datos): Mesa;
    
    /**
     * Actualizar mesa existente
     * 
     * @param int $id
     * @param array $datos
     * @return Mesa
     */
    public function actualizar(int $id, array $datos): Mesa;
    
    /**
     * Eliminar mesa
     * 
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool;
    
    /**
     * Cambiar estado de mesa
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
}