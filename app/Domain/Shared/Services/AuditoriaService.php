<?php

namespace App\Domain\Shared\Services;

use Illuminate\Support\Facades\DB;

class AuditoriaService
{
    public function registrar(
        string $tabla,
        int $registroId,
        string $accion,
        ?int $usuarioId = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ): void {
        try {
            DB::table('core_auditoria')->insert([
                'tabla' => $tabla,
                'registro_id' => $registroId,
                'accion' => $accion,
                'usuario_id' => $usuarioId ?? auth()->id(),
                'datos_anteriores' => $datosAnteriores ? json_encode($datosAnteriores) : null,
                'datos_nuevos' => $datosNuevos ? json_encode($datosNuevos) : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Log del error pero no detener el flujo
            \Log::error('Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}