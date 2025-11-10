<?php

namespace App\Domain\Restaurante\Requests;

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
            'zona_restaurante_id' => 'nullable|integer|exists:zonas_restaurante,id',
            'identificacion_mesa' => 'sometimes|required|string|max:50',
            'numero_mesa' => 'nullable|integer|min:1',
            'capacidad' => 'sometimes|required|integer|min:1|max:50',
            'estado_id' => 'sometimes|required|integer|exists:estados_mesa,id',
            'staff_asignado_id' => 'nullable|integer|exists:staff,id',
            'qr_code' => 'nullable|string|max:255',
            'activa' => 'boolean',
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
