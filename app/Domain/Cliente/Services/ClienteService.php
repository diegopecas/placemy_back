<?php

namespace App\Domain\Cliente\Services;

use App\Domain\Cliente\Contracts\ClienteServiceInterface;
use App\Domain\Cliente\Repositories\ClienteRepository;
use App\Domain\Cliente\Models\Cliente;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class ClienteService implements ClienteServiceInterface
{
    protected $clienteRepository;
    protected $auditoriaService;
    
    public function __construct(
        ClienteRepository $clienteRepository,
        AuditoriaService $auditoriaService
    ) {
        $this->clienteRepository = $clienteRepository;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar clientes con filtros (búsqueda)
     */
    public function listar(array $filtros = []): array
    {
        $clientes = $this->clienteRepository->findWithFilters($filtros);
        
        return $clientes->map(function ($cliente) {
            return $this->formatearCliente($cliente);
        })->toArray();
    }
    
    /**
     * Obtener cliente por ID
     */
    public function obtenerPorId(int $id): array
    {
        $cliente = $this->clienteRepository->findByIdWithRelations($id);
        
        if (!$cliente) {
            throw new NotFoundException('Cliente no encontrado');
        }
        
        return $this->formatearCliente($cliente);
    }
    
    /**
     * Crear cliente DIRECTO (sin persona)
     * NUEVA ESTRUCTURA
     */
    public function crear(array $data): array
    {
        DB::beginTransaction();
        try {
            // Validar campos obligatorios
            if (!isset($data['nombre']) || empty($data['nombre'])) {
                throw new BusinessException('El nombre es obligatorio');
            }
            
            if (!isset($data['telefono']) || empty($data['telefono'])) {
                throw new BusinessException('El teléfono es obligatorio');
            }
            
            // Crear cliente
            $datosCrear = [
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'numero_documento' => $data['numero_documento'] ?? null,
                'tipo_documento_id' => $data['tipo_documento_id'] ?? null,
                'email' => $data['email'] ?? null,
                'sexo' => $data['sexo'] ?? null,
                'dia_cumpleanos' => $data['dia_cumpleanos'] ?? null,
                'mes_cumpleanos' => $data['mes_cumpleanos'] ?? null,
                'sobrenombre' => $data['sobrenombre'] ?? null,
                'preferencias_gustos' => $data['preferencias_gustos'] ?? null,
                'preferencias_no_gustos' => $data['preferencias_no_gustos'] ?? null,
                'otras_alergias' => $data['otras_alergias'] ?? null,
            ];
            
            $cliente = $this->clienteRepository->create($datosCrear);
            
            // Asociar alérgenos si vienen
            if (isset($data['alergenos']) && is_array($data['alergenos'])) {
                $cliente->alergenos()->sync($data['alergenos']);
            }
            
            // Auditoría
            $this->auditoriaService->registrar(
                'clientes',
                $cliente->id,
                'INSERT',
                auth()->id(),
                null,
                $cliente->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $cliente = $this->clienteRepository->findByIdWithRelations($cliente->id);
            return $this->formatearCliente($cliente);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Actualizar cliente
     */
    public function actualizar(int $id, array $data): array
    {
        $cliente = $this->clienteRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $cliente->toArray();
            
            // Actualizar campos permitidos
            $datosActualizar = [];
            
            $camposPermitidos = [
                'nombre',
                'telefono',
                'numero_documento',
                'tipo_documento_id',
                'email',
                'sexo',
                'dia_cumpleanos',
                'mes_cumpleanos',
                'sobrenombre',
                'preferencias_gustos',
                'preferencias_no_gustos',
                'otras_alergias'
            ];
            
            foreach ($camposPermitidos as $campo) {
                if (isset($data[$campo])) {
                    $datosActualizar[$campo] = $data[$campo];
                }
            }
            
            if (!empty($datosActualizar)) {
                $clienteActualizado = $this->clienteRepository->update($id, $datosActualizar);
            } else {
                $clienteActualizado = $cliente;
            }
            
            // Sincronizar alérgenos si vienen
            if (isset($data['alergenos']) && is_array($data['alergenos'])) {
                $clienteActualizado->alergenos()->sync($data['alergenos']);
            }
            
            // Auditoría
            $this->auditoriaService->registrar(
                'clientes',
                $id,
                'UPDATE',
                auth()->id(),
                $datosAnteriores,
                $clienteActualizado->toArray()
            );
            
            DB::commit();
            
            // Recargar con relaciones
            $clienteActualizado = $this->clienteRepository->findByIdWithRelations($id);
            return $this->formatearCliente($clienteActualizado);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Eliminar cliente
     */
    public function eliminar(int $id): bool
    {
        $cliente = $this->clienteRepository->findByIdOrFail($id);
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $cliente->toArray();
            
            // Eliminar
            $this->clienteRepository->delete($id);
            
            // Auditoría
            $this->auditoriaService->registrar(
                'clientes',
                $id,
                'DELETE',
                auth()->id(),
                $datosAnteriores,
                null
            );
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Formatear cliente para respuesta
     * ACTUALIZADO: Prioriza campos directos sobre persona
     */
    private function formatearCliente(Cliente $cliente): array
    {
        return [
            'id' => $cliente->id,
            
            // CAMPOS DIRECTOS (nueva estructura)
            'nombre' => $cliente->nombre ?? null,
            'telefono' => $cliente->telefono ?? null,
            'numero_documento' => $cliente->numero_documento ?? null,
            'tipo_documento_id' => $cliente->tipo_documento_id ?? null,
            'email' => $cliente->email ?? null,
            'sexo' => $cliente->sexo ?? null,
            'dia_cumpleanos' => $cliente->dia_cumpleanos ?? null,
            'mes_cumpleanos' => $cliente->mes_cumpleanos ?? null,
            
            // CAMPOS EXISTENTES
            'persona_id' => $cliente->persona_id,
            'sobrenombre' => $cliente->sobrenombre,
            'preferencias_gustos' => $cliente->preferencias_gustos,
            'preferencias_no_gustos' => $cliente->preferencias_no_gustos,
            'otras_alergias' => $cliente->otras_alergias,
            
            // RELACIÓN PERSONA (solo si existe)
            'persona' => $cliente->persona ? [
                'id' => $cliente->persona->id,
                'nombre_completo' => trim(
                    $cliente->persona->primer_nombre . ' ' . 
                    ($cliente->persona->segundo_nombre ?? '') . ' ' .
                    $cliente->persona->primer_apellido . ' ' .
                    ($cliente->persona->segundo_apellido ?? '')
                ),
                'numero_documento' => $cliente->persona->numero_documento,
                'telefono' => $cliente->persona->telefono,
                'email' => $cliente->persona->email,
            ] : null,
            
            // ALÉRGENOS
            'alergenos' => $cliente->alergenos->map(function($alergeno) {
                return [
                    'id' => $alergeno->id,
                    'nombre' => $alergeno->nombre,
                    'icono' => $alergeno->icono,
                ];
            })->toArray(),
            
            // TIMESTAMPS
            'created_at' => $cliente->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $cliente->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}