<?php

namespace App\Domain\Cuenta\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateCuentaItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('cuenta_items.crear');
    }

    public function rules(): array
    {
        return [
            'cuenta_id' => 'required|integer|exists:cuentas,id',
            'establecimiento_plato_id' => [
                'nullable',
                'integer',
                'exists:establecimiento_platos,id',
                Rule::requiredIf(function () {
                    return empty($this->input('establecimiento_producto_id'));
                })
            ],
            'establecimiento_producto_id' => [
                'nullable',
                'integer',
                'exists:establecimiento_productos,id',
                Rule::requiredIf(function () {
                    return empty($this->input('establecimiento_plato_id'));
                })
            ],
            'cantidad' => 'required|integer|min:1',
            'precio_unitario' => 'required|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'notas_especiales' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'cuenta_id.required' => 'La cuenta es obligatoria',
            'cuenta_id.exists' => 'La cuenta seleccionada no existe',
            'establecimiento_plato_id.exists' => 'El plato seleccionado no existe',
            'establecimiento_producto_id.exists' => 'El producto seleccionado no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo',
            'descuento.min' => 'El descuento no puede ser negativo',
        ];
    }
}
