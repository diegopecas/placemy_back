<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateEstablecimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificar permiso
        return $this->user()->hasPermission('establecimientos.crear');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Datos básicos
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:establecimientos,slug',
            'descripcion' => 'nullable|string|max:1000',
            
            // Ubicación
            'direccion' => 'required|string|max:500',
            'ciudad_id' => 'required|integer|exists:core_ciudades,id',
            
            // Contacto
            'telefono' => 'required|string|max:20',
            'email_contacto' => 'nullable|email|max:150',
            
            // Información comercial
            'rango_precio_id' => 'nullable|integer|exists:rangos_precio,id',
            'capacidad_total' => 'nullable|integer|min:0',
            
            // Horarios (JSON)
            'horario_apertura' => 'nullable|json',
            
            // URLs
            'logo_url' => 'nullable|url|max:500',
            'banner_url' => 'nullable|url|max:500',
            'url_menu' => 'nullable|url|max:500',
            
            // Relaciones
            'persona_juridica_id' => 'required|integer|exists:core_personas_juridicas,id',
            
            // Estado
            'activo' => 'boolean',
            'verificado' => 'boolean',
            'fecha_apertura' => 'nullable|date',
            
            // Arrays de IDs (many-to-many)
            'tipos_cocina' => 'nullable|array',
            'tipos_cocina.*' => 'integer|exists:tipos_cocina,id',
            
            'metodos_pago' => 'nullable|array',
            'metodos_pago.*' => 'integer|exists:metodos_pago,id',
            
            'caracteristicas' => 'nullable|array',
            'caracteristicas.*' => 'integer|exists:caracteristicas_establecimiento,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del establecimiento es obligatorio',
            'slug.required' => 'El slug es obligatorio',
            'slug.unique' => 'Ya existe un establecimiento con este slug',
            'direccion.required' => 'La dirección es obligatoria',
            'ciudad_id.required' => 'La ciudad es obligatoria',
            'ciudad_id.exists' => 'La ciudad seleccionada no existe',
            'telefono.required' => 'El teléfono es obligatorio',
            'persona_juridica_id.required' => 'La persona jurídica es obligatoria',
            'persona_juridica_id.exists' => 'La persona jurídica seleccionada no existe',
        ];
    }
}
