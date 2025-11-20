<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('clientes.editar');
    }

    public function rules(): array
    {
        return [
            'sobrenombre' => 'sometimes|nullable|string|max:100',
            'preferencias_gustos' => 'sometimes|nullable|string',
            'preferencias_no_gustos' => 'sometimes|nullable|string',
            'otras_alergias' => 'sometimes|nullable|string',
            
            // Alérgenos (array de IDs)
            'alergenos' => 'sometimes|nullable|array',
            'alergenos.*' => 'integer|exists:alergenos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'sobrenombre.max' => 'El sobrenombre no puede tener más de 100 caracteres',
        ];
    }
}
