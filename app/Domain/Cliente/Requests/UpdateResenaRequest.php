<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('resenas.editar');
    }

    public function rules(): array
    {
        return [
            'calificacion' => 'sometimes|required|integer|min:1|max:5',
            'comentario' => 'sometimes|nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'calificacion.required' => 'La calificación es obligatoria',
            'calificacion.min' => 'La calificación debe ser al menos 1',
            'calificacion.max' => 'La calificación debe ser máximo 5',
            'comentario.max' => 'El comentario no puede tener más de 1000 caracteres',
        ];
    }
}
