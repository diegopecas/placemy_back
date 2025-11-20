<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCampaniaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('campanias.editar');
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'descripcion' => 'sometimes|nullable|string',
            'icono' => 'sometimes|nullable|string|max:100',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|required|date|after_or_equal:fecha_inicio',
            'configuracion_json' => 'sometimes|nullable|json',
            'activo' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
        ];
    }
}
