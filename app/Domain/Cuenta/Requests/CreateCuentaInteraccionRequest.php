<?php

namespace App\Domain\Cuenta\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCuentaInteraccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Las interacciones pueden ser creadas por clientes o staff
        return true;
    }

    public function rules(): array
    {
        return [
            'cuenta_id' => 'required|integer|exists:cuentas,id',
            'tipo_interaccion_id' => 'required|integer|exists:tipos_interacciones,id',
            'valor_numerico' => 'nullable|integer|min:0|max:100',
            'mensaje' => 'nullable|string|max:1000',
            'opcion_seleccionada' => 'nullable|string|max:100',
            'foto_url' => 'nullable|url|max:500',
            'fecha_interaccion' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'cuenta_id.required' => 'La cuenta es obligatoria',
            'cuenta_id.exists' => 'La cuenta seleccionada no existe',
            'tipo_interaccion_id.required' => 'El tipo de interacción es obligatorio',
            'tipo_interaccion_id.exists' => 'El tipo de interacción seleccionado no existe',
            'valor_numerico.min' => 'El valor numérico debe ser entre 0 y 100',
            'valor_numerico.max' => 'El valor numérico debe ser entre 0 y 100',
            'foto_url.url' => 'La URL de la foto no es válida',
        ];
    }
}
