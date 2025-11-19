<?php

namespace App\Domain\Establecimiento\Requests;

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
                'max:20',
                Rule::unique('staff', 'codigo_empleado')->ignore($staffId)
            ],
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_empleado.required' => 'El código de empleado es obligatorio',
            'codigo_empleado.unique' => 'Ya existe un empleado con este código',
        ];
    }
}
