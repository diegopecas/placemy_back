<?php

namespace App\Domain\Restaurante\Requests;

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
            'restaurante_id' => 'required|integer|exists:restaurantes,id',
            'zona_restaurante_id' => 'nullable|integer|exists:zonas_restaurante,id',
            'identificacion_mesa' => 'required|string|max:50',
            'numero_mesa' => 'nullable|integer|min:1',
            'capacidad' => 'required|integer|min:1|max:50',
            'estado_id' => 'required|integer|exists:estados_mesa,id',
            'staff_asignado_id' => 'nullable|integer|exists:staff,id',
            'qr_code' => 'nullable|string|max:255',
            'activa' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'restaurante_id.required' => 'El restaurante es obligatorio',
            'restaurante_id.exists' => 'El restaurante seleccionado no existe',
            'identificacion_mesa.required' => 'La identificación de la mesa es obligatoria',
            'capacidad.required' => 'La capacidad es obligatoria',
            'capacidad.min' => 'La capacidad debe ser al menos 1',
            'estado_id.required' => 'El estado es obligatorio',
        ];
    }
}
