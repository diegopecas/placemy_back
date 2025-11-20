<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClienteCompletoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('clientes.crear');
    }

    public function rules(): array
    {
        return [
            // Datos de persona (para buscar o crear)
            'tipo_documento_id' => 'required|integer|exists:core_tipos_documento,id',
            'numero_documento' => 'required|string|max:50',
            'primer_nombre' => 'required|string|max:100',
            'segundo_nombre' => 'nullable|string|max:100',
            'primer_apellido' => 'required|string|max:100',
            'segundo_apellido' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'nullable|date',
            'sexo' => 'nullable|in:M,F,O',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            
            // Datos de cliente
            'sobrenombre' => 'nullable|string|max:100',
            'preferencias_gustos' => 'nullable|string',
            'preferencias_no_gustos' => 'nullable|string',
            'otras_alergias' => 'nullable|string',
            
            // Alérgenos
            'alergenos' => 'nullable|array',
            'alergenos.*' => 'integer|exists:alergenos,id',
            
            // Establecimiento (opcional)
            'establecimiento_id' => 'nullable|integer|exists:establecimientos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento_id.required' => 'El tipo de documento es obligatorio',
            'numero_documento.required' => 'El número de documento es obligatorio',
            'primer_nombre.required' => 'El primer nombre es obligatorio',
            'primer_apellido.required' => 'El primer apellido es obligatorio',
            'email.email' => 'El email debe ser válido',
        ];
    }
}
