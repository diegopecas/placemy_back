<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlatoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('platos.editar');
    }

    public function rules(): array
    {
        $platoId = $this->route('id');
        
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'codigo_plato' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('platos', 'codigo_plato')->ignore($platoId)
            ],
            'descripcion' => 'nullable|string|max:1000',
            'categoria_id' => 'sometimes|required|integer|exists:categorias_menu,id',
            'costo' => 'nullable|numeric|min:0',
            'foto_url' => 'nullable|url|max:500',
            'video_url' => 'nullable|url|max:500',
            'tiempo_preparacion_min' => 'nullable|integer|min:0',
            'etiquetas' => 'nullable|array',
            
            // Alérgenos
            'alergenos' => 'nullable|array',
            'alergenos.*' => 'integer|exists:alergenos,id',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del plato es obligatorio',
            'codigo_plato.unique' => 'Ya existe un plato con este código',
        ];
    }
}
