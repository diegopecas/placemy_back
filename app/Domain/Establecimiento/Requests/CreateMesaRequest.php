<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateMesaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('mesas.crear');
    }

    public function rules(): array
    {
        return [
            'establecimiento_id' => 'required|integer|exists:establecimientos,id',
            'zona_id' => 'nullable|integer|exists:zonas_establecimiento,id',
            'identificacion_mesa' => 'required|string|max:200',
            'capacidad' => 'required|integer|min:1|max:50',
            'estado_id' => 'required|integer|exists:estados_mesa,id',
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
            'establecimiento_id.required' => 'El establecimiento es obligatorio',
            'establecimiento_id.exists' => 'El establecimiento seleccionado no existe',
            'identificacion_mesa.required' => 'La identificación de la mesa es obligatoria',
            'capacidad.required' => 'La capacidad es obligatoria',
            'capacidad.min' => 'La capacidad debe ser al menos 1',
            'estado_id.required' => 'El estado es obligatorio',
        ];
    }
}
