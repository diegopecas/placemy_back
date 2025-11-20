<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClienteEstablecimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('clientes.asociar_establecimiento');
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|integer|exists:clientes,id',
            'establecimiento_id' => 'required|integer|exists:establecimientos,id',
            'zona_preferida_id' => 'nullable|integer|exists:zonas_establecimiento,id',
            'notas_internas' => 'nullable|string',
            'acepta_promociones' => 'boolean',
            'fecha_primera_visita' => 'nullable|date',
            'calificacion_interna' => 'nullable|integer|min:1|max:5',
            'motivo_calificacion' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'El cliente es obligatorio',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'establecimiento_id.required' => 'El establecimiento es obligatorio',
            'establecimiento_id.exists' => 'El establecimiento seleccionado no existe',
            'calificacion_interna.min' => 'La calificación debe ser al menos 1',
            'calificacion_interna.max' => 'La calificación debe ser máximo 5',
        ];
    }
}
