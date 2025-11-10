<?php

namespace App\Domain\Restaurante\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePlatoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('platos.crear');
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'codigo_plato' => 'nullable|string|max:50|unique:platos,codigo_plato',
            'descripcion' => 'nullable|string|max:1000',
            'categoria_menu_id' => 'required|integer|exists:categorias_menu,id',
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
            'categoria_menu_id.required' => 'La categoría es obligatoria',
            'categoria_menu_id.exists' => 'La categoría seleccionada no existe',
        ];
    }
}
