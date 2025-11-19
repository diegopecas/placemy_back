<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('staff.crear');
    }

    public function rules(): array
    {
        return [
            'persona_id' => 'required|integer|exists:core_personas_naturales,id',
            'codigo_empleado' => 'required|string|max:20|unique:staff,codigo_empleado',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria',
            'persona_id.exists' => 'La persona seleccionada no existe',
            'codigo_empleado.required' => 'El código de empleado es obligatorio',
            'codigo_empleado.unique' => 'Ya existe un empleado con este código',
        ];
    }
}
