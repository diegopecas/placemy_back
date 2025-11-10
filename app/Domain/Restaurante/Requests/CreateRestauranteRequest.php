<?php

namespace App\Domain\Restaurante\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateRestauranteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Verificar permiso
        return $this->user()->hasPermission('restaurantes.crear');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Datos básicos
            'nombre' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:restaurantes,slug',
            'descripcion' => 'nullable|string|max:1000',
            
            // Ubicación
            'direccion' => 'required|string|max:500',
            'barrio' => 'nullable|string|max:100',
            'ciudad_id' => 'required|integer|exists:core_ciudades,id',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            
            // Contacto
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'sitio_web' => 'nullable|url|max:255',
            
            // Información comercial
            'tipo_cocina_id' => 'required|integer|exists:tipos_cocina,id',
            'rango_precio_id' => 'required|integer|exists:rangos_precio,id',
            'capacidad_total' => 'required|integer|min:1',
            
            // Horarios (JSON)
            'horarios' => 'nullable|json',
            
            // Relaciones
            'persona_juridica_id' => 'nullable|integer|exists:core_personas_juridicas,id',
            'grupo_empresarial_id' => 'nullable|integer|exists:grupos_empresariales,id',
            
            // Estado
            'activo' => 'boolean',
            
            // Arrays de IDs (many-to-many)
            'metodos_pago' => 'nullable|array',
            'metodos_pago.*' => 'integer|exists:metodos_pago,id',
            
            'caracteristicas' => 'nullable|array',
            'caracteristicas.*' => 'integer|exists:caracteristicas_restaurante,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del restaurante es obligatorio',
            'slug.required' => 'El slug es obligatorio',
            'slug.unique' => 'Ya existe un restaurante con este slug',
            'direccion.required' => 'La dirección es obligatoria',
            'ciudad_id.required' => 'La ciudad es obligatoria',
            'ciudad_id.exists' => 'La ciudad seleccionada no existe',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser válido',
            'tipo_cocina_id.required' => 'El tipo de cocina es obligatorio',
            'tipo_cocina_id.exists' => 'El tipo de cocina seleccionado no existe',
            'rango_precio_id.required' => 'El rango de precio es obligatorio',
            'capacidad_total.required' => 'La capacidad es obligatoria',
            'capacidad_total.min' => 'La capacidad debe ser al menos 1',
        ];
    }
}
