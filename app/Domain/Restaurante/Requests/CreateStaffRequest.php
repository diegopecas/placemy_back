<?php

namespace App\Domain\Restaurante\Requests;

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
            'codigo_empleado' => 'required|string|max:50|unique:staff,codigo_empleado',
            'fecha_ingreso' => 'required|date',
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
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser válida',
        ];
    }
}
