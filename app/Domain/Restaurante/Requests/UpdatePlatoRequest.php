<?php

namespace App\Domain\Restaurante\Requests;

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
            'categoria_menu_id' => 'sometimes|required|integer|exists:categorias_menu,id',
            'imagen_url' => 'nullable|url|max:500',
            'tiempo_preparacion' => 'nullable|integer|min:0',
            'calorias' => 'nullable|integer|min:0',
            'es_vegetariano' => 'boolean',
            'es_vegano' => 'boolean',
            'sin_gluten' => 'boolean',
            'picante' => 'boolean',
            'activo' => 'boolean',
            
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
