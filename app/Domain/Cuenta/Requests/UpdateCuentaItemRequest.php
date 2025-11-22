<?php

namespace App\Domain\Cuenta\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCuentaItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('cuenta_items.actualizar');
    }

    public function rules(): array
    {
        return [
            'cantidad' => 'nullable|integer|min:1',
            'notas_especiales' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'La cantidad debe ser al menos 1',
            'notas_especiales.max' => 'Las notas especiales no pueden exceder 500 caracteres',
        ];
    }
}
