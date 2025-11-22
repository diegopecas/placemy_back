<?php

namespace App\Domain\Cuenta\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCuentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('cuentas.crear');
    }

    public function rules(): array
    {
        return [
            'establecimiento_id' => 'required|integer|exists:establecimientos,id',
            'mesa_id' => 'required|integer|exists:mesas,id',
            'establecimiento_staff_id' => 'required|integer|exists:establecimiento_staff,id',
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'estado_id' => 'required|integer|exists:cuenta_estados,id',
            'incluir_impuestos' => 'boolean',
            'notas_cliente' => 'nullable|string|max:1000',
            'notas_internas' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'establecimiento_id.required' => 'El establecimiento es obligatorio',
            'establecimiento_id.exists' => 'El establecimiento seleccionado no existe',
            'mesa_id.required' => 'La mesa es obligatoria',
            'mesa_id.exists' => 'La mesa seleccionada no existe',
            'establecimiento_staff_id.required' => 'El mesero es obligatorio',
            'establecimiento_staff_id.exists' => 'El mesero seleccionado no existe',
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'estado_id.required' => 'El estado es obligatorio',
            'estado_id.exists' => 'El estado seleccionado no existe',
        ];
    }
}
