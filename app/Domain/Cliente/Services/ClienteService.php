<?php

namespace App\Domain\Cliente\Services;

use App\Domain\Cliente\Contracts\ClienteServiceInterface;
use App\Domain\Cliente\Repositories\ClienteRepository;
use App\Domain\Cliente\Models\Cliente;
use App\Domain\Core\Services\PersonaNaturalService;
use App\Domain\Shared\Exceptions\BusinessException;
use App\Domain\Shared\Exceptions\NotFoundException;
use App\Domain\Shared\Services\AuditoriaService;
use Illuminate\Support\Facades\DB;

class ClienteService implements ClienteServiceInterface
{
    protected $clienteRepository;
    protected $personaNaturalService;
    protected $auditoriaService;
    
    public function __construct(
        ClienteRepository $clienteRepository,
        PersonaNaturalService $personaNaturalService,
        AuditoriaService $auditoriaService
    ) {
        $this->clienteRepository = $clienteRepository;
        $this->personaNaturalService = $personaNaturalService;
        $this->auditoriaService = $auditoriaService;
    }
    
    /**
     * Listar clientes
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
     * Crear cliente básico
     */
    public function crear(array $data): array
    {
        // Validar que la persona existe
        if (!isset($data['persona_id'])) {
            throw new BusinessException('El persona_id es obligatorio');
        }
        
        // Validar que no existe cliente con esa persona
        if ($this->clienteRepository->existePersona($data['persona_id'])) {
            throw new BusinessException('Ya existe un cliente registrado con esta persona');
        }
        
        DB::beginTransaction();
        try {
            // Crear cliente
            $datosCrear = [
                'persona_id' => $data['persona_id'],
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
     * Crear cliente completo (orquestador)
     */
    public function crearCompleto(array $data): array
    {
        DB::beginTransaction();
        try {
            // 1. Buscar o crear persona
            $personaId = $this->buscarOCrearPersona($data);
            
            // 2. Crear cliente básico
            $dataCliente = array_merge($data, ['persona_id' => $personaId]);
            $cliente = $this->crear($dataCliente);
            
            // 3. Asociar a establecimiento si viene
            if (isset($data['establecimiento_id'])) {
                // Aquí se llamaría al service de ClienteEstablecimiento
                // pero como estamos en el mismo service, lo dejamos pendiente
                // para que lo maneje el controller
            }
            
            DB::commit();
            return $cliente;
            
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
            
            // Actualizar datos básicos
            $datosActualizar = [];
            
            if (isset($data['sobrenombre'])) {
                $datosActualizar['sobrenombre'] = $data['sobrenombre'];
            }
            
            if (isset($data['preferencias_gustos'])) {
                $datosActualizar['preferencias_gustos'] = $data['preferencias_gustos'];
            }
            
            if (isset($data['preferencias_no_gustos'])) {
                $datosActualizar['preferencias_no_gustos'] = $data['preferencias_no_gustos'];
            }
            
            if (isset($data['otras_alergias'])) {
                $datosActualizar['otras_alergias'] = $data['otras_alergias'];
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
     * Eliminar cliente (soft delete)
     */
    public function eliminar(int $id): bool
    {
        $cliente = $this->clienteRepository->findByIdOrFail($id);
        
        // Verificar que no tenga asociaciones activas con establecimientos
        if ($cliente->establecimientos()->count() > 0) {
            throw new BusinessException('No se puede eliminar el cliente porque tiene asociaciones con establecimientos');
        }
        
        DB::beginTransaction();
        try {
            $datosAnteriores = $cliente->toArray();
            
            // Eliminar (delete real por ahora, no hay campo "activo")
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
     * Buscar o crear persona
     */
    private function buscarOCrearPersona(array $data): int
    {
        // Buscar por tipo_documento + numero_documento
        $personaExistente = \App\Domain\Core\Models\PersonaNatural::where('tipo_documento_id', $data['tipo_documento_id'])
            ->where('numero_documento', $data['numero_documento'])
            ->first();
        
        if ($personaExistente) {
            return $personaExistente->id;
        }
        
        // Crear nueva persona
        $datosPersona = [
            'tipo_documento_id' => $data['tipo_documento_id'],
            'numero_documento' => $data['numero_documento'],
            'primer_nombre' => $data['primer_nombre'],
            'segundo_nombre' => $data['segundo_nombre'] ?? null,
            'primer_apellido' => $data['primer_apellido'],
            'segundo_apellido' => $data['segundo_apellido'] ?? null,
            'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,
            'sexo' => $data['sexo'] ?? null,
            'telefono' => $data['telefono'] ?? null,
            'email' => $data['email'] ?? null,
        ];
        
        $persona = $this->personaNaturalService->crear($datosPersona);
        return $persona['id'];
    }
    
    /**
     * Formatear cliente para respuesta
     */
    private function formatearCliente(Cliente $cliente): array
    {
        return [
            'id' => $cliente->id,
            'persona_id' => $cliente->persona_id,
            'sobrenombre' => $cliente->sobrenombre,
            'preferencias_gustos' => $cliente->preferencias_gustos,
            'preferencias_no_gustos' => $cliente->preferencias_no_gustos,
            'otras_alergias' => $cliente->otras_alergias,
            'persona' => $cliente->persona ? [
                'id' => $cliente->persona->id,
                'nombre_completo' => $cliente->persona->primer_nombre . ' ' . 
                                     ($cliente->persona->segundo_nombre ? $cliente->persona->segundo_nombre . ' ' : '') .
                                     $cliente->persona->primer_apellido . ' ' .
                                     ($cliente->persona->segundo_apellido ?? ''),
                'numero_documento' => $cliente->persona->numero_documento,
                'telefono' => $cliente->persona->telefono,
                'email' => $cliente->persona->email,
            ] : null,
            'alergenos' => $cliente->alergenos->map(function($alergeno) {
                return [
                    'id' => $alergeno->id,
                    'nombre' => $alergeno->nombre,
                    'icono' => $alergeno->icono,
                ];
            })->toArray(),
            'fechas_especiales' => $cliente->fechasEspeciales->map(function($fecha) {
                return [
                    'id' => $fecha->id,
                    'tipo_fecha_id' => $fecha->tipo_fecha_id,
                    'tipo_fecha' => $fecha->tipoFecha ? $fecha->tipoFecha->nombre : null,
                    'fecha' => $fecha->fecha->format('Y-m-d'),
                    'descripcion' => $fecha->descripcion,
                ];
            })->toArray(),
            'establecimientos_count' => $cliente->establecimientos->count(),
            'created_at' => $cliente->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $cliente->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
