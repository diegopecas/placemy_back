<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('clientes.crear');
    }

    public function rules(): array
    {
        return [
            'persona_id' => 'required|integer|exists:core_personas_naturales,id',
            'sobrenombre' => 'nullable|string|max:100',
            'preferencias_gustos' => 'nullable|string',
            'preferencias_no_gustos' => 'nullable|string',
            'otras_alergias' => 'nullable|string',
            
            // Alérgenos (array de IDs)
            'alergenos' => 'nullable|array',
            'alergenos.*' => 'integer|exists:alergenos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'persona_id.required' => 'La persona es obligatoria',
            'persona_id.exists' => 'La persona seleccionada no existe',
            'sobrenombre.max' => 'El sobrenombre no puede tener más de 100 caracteres',
        ];
    }
}
