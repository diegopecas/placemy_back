<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteEstablecimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('clientes.editar_establecimiento');
    }

    public function rules(): array
    {
        return [
            'zona_preferida_id' => 'sometimes|nullable|integer|exists:zonas_establecimiento,id',
            'notas_internas' => 'sometimes|nullable|string',
            'acepta_promociones' => 'sometimes|boolean',
            'calificacion_interna' => 'sometimes|nullable|integer|min:1|max:5',
            'motivo_calificacion' => 'sometimes|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'calificacion_interna.min' => 'La calificación debe ser al menos 1',
            'calificacion_interna.max' => 'La calificación debe ser máximo 5',
        ];
    }
}
