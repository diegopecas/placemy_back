<?php

namespace App\Domain\Cliente\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCampaniaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('campanias.crear');
    }

    public function rules(): array
    {
        return [
            'establecimiento_id' => 'required|integer|exists:establecimientos,id',
            'tipo_campania_id' => 'required|integer|exists:tipos_campania,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'icono' => 'nullable|string|max:100',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'configuracion_json' => 'nullable|json',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'establecimiento_id.required' => 'El establecimiento es obligatorio',
            'tipo_campania_id.required' => 'El tipo de campaña es obligatorio',
            'nombre.required' => 'El nombre es obligatorio',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio',
        ];
    }
}
