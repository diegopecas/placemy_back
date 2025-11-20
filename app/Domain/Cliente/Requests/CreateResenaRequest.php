<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateResenaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('resenas.crear');
    }

    public function rules(): array
    {
        return [
            'cliente_establecimiento_id' => 'required|integer|exists:cliente_establecimiento,id',
            'calificacion' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000',
            'fecha_resena' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_establecimiento_id.required' => 'La asociación cliente-establecimiento es obligatoria',
            'cliente_establecimiento_id.exists' => 'La asociación seleccionada no existe',
            'calificacion.required' => 'La calificación es obligatoria',
            'calificacion.min' => 'La calificación debe ser al menos 1',
            'calificacion.max' => 'La calificación debe ser máximo 5',
            'comentario.max' => 'El comentario no puede tener más de 1000 caracteres',
        ];
    }
}
