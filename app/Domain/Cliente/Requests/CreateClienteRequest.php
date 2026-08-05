<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateClienteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // CAMPOS OBLIGATORIOS
            'nombre' => 'required|string|max:200',
            'telefono' => 'required|string|max:20',
            
            // CAMPOS OPCIONALES
            'numero_documento' => 'nullable|string|max:50',
            'tipo_documento_id' => 'nullable|integer|exists:core_tipos_documento,id',
            'email' => 'nullable|email|max:150',
            'sexo' => 'nullable|in:M,F,O',
            'dia_cumpleanos' => 'nullable|integer|min:1|max:31',
            'mes_cumpleanos' => 'nullable|integer|min:1|max:12',
            'sobrenombre' => 'nullable|string|max:100',
            'preferencias_gustos' => 'nullable|string',
            'preferencias_no_gustos' => 'nullable|string',
            'otras_alergias' => 'nullable|string',
            'alergenos' => 'nullable|array',
            'alergenos.*' => 'integer|exists:alergenos,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre es obligatorio',
            'nombre.max' => 'El nombre no puede tener más de 200 caracteres',
            'telefono.required' => 'El teléfono es obligatorio',
            'telefono.max' => 'El teléfono no puede tener más de 20 caracteres',
            'email.email' => 'El email debe ser válido',
            'sexo.in' => 'El sexo debe ser M, F u O',
            'dia_cumpleanos.min' => 'El día debe estar entre 1 y 31',
            'dia_cumpleanos.max' => 'El día debe estar entre 1 y 31',
            'mes_cumpleanos.min' => 'El mes debe estar entre 1 y 12',
            'mes_cumpleanos.max' => 'El mes debe estar entre 1 y 12',
        ];
    }
}