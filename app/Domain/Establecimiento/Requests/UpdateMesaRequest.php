<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('mesas.editar');
    }

    public function rules(): array
    {
        return [
            'zona_id' => 'nullable|integer|exists:zonas_establecimiento,id',
            'identificacion_mesa' => 'sometimes|required|string|max:200',
            'capacidad' => 'sometimes|required|integer|min:1|max:50',
            'estado_id' => 'sometimes|required|integer|exists:estados_mesa,id',
            'staff_asignado_id' => 'nullable|integer|exists:staff,id',
            'forma' => 'nullable|string|max:20',
            'posicion_x' => 'nullable|integer',
            'posicion_y' => 'nullable|integer',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'identificacion_mesa.required' => 'La identificación de la mesa es obligatoria',
            'capacidad.min' => 'La capacidad debe ser al menos 1',
        ];
    }
}
