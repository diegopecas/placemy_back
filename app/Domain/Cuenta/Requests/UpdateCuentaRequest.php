<?php

namespace App\Domain\Cuenta\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('cuentas.actualizar');
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'nullable|integer|exists:clientes,id',
            'descuento' => 'nullable|numeric|min:0',
            'propina' => 'nullable|numeric|min:0',
            'propina_porcentaje' => 'nullable|numeric|min:0|max:100',
            'notas_cliente' => 'nullable|string|max:1000',
            'notas_internas' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'El cliente seleccionado no existe',
            'descuento.numeric' => 'El descuento debe ser un número',
            'descuento.min' => 'El descuento no puede ser negativo',
            'propina.numeric' => 'La propina debe ser un número',
            'propina.min' => 'La propina no puede ser negativa',
            'propina_porcentaje.numeric' => 'El porcentaje de propina debe ser un número',
            'propina_porcentaje.min' => 'El porcentaje de propina no puede ser negativo',
            'propina_porcentaje.max' => 'El porcentaje de propina no puede exceder 100%',
        ];
    }
}
