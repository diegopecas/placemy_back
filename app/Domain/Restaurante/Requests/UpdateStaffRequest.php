<?php

namespace App\Domain\Restaurante\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('staff.editar');
    }

    public function rules(): array
    {
        $staffId = $this->route('id');
        
        return [
            'codigo_empleado' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('staff', 'codigo_empleado')->ignore($staffId)
            ],
            'fecha_ingreso' => 'sometimes|required|date',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_empleado.required' => 'El código de empleado es obligatorio',
            'codigo_empleado.unique' => 'Ya existe un empleado con este código',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser válida',
        ];
    }
}
