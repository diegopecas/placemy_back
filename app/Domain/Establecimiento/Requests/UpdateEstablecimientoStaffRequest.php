<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstablecimientoStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('restaurante_staff.editar');
    }

    public function rules(): array
    {
        return [
            'cargo_id' => 'sometimes|required|integer|exists:cargos,id',
            'codigo_empleado' => 'sometimes|required|string|max:20',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'cargo_id.required' => 'El cargo es obligatorio',
            'cargo_id.exists' => 'El cargo seleccionado no existe',
            'codigo_empleado.required' => 'El código de empleado es obligatorio',
            'codigo_empleado.max' => 'El código de empleado no puede tener más de 20 caracteres',
        ];
    }
}