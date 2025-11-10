<?php

namespace App\Domain\Restaurante\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRestauranteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('restaurantes.editar');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $restauranteId = $this->route('id');
        
        return [
            // Datos básicos
            'nombre' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('restaurantes', 'slug')->ignore($restauranteId)
            ],
            'descripcion' => 'nullable|string|max:1000',
            
            // Ubicación
            'direccion' => 'sometimes|required|string|max:500',
            'barrio' => 'nullable|string|max:100',
            'ciudad_id' => 'sometimes|required|integer|exists:core_ciudades,id',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
            
            // Contacto
            'telefono' => 'sometimes|required|string|max:20',
            'email' => 'sometimes|required|email|max:255',
            'sitio_web' => 'nullable|url|max:255',
            
            // Información comercial
            'tipo_cocina_id' => 'sometimes|required|integer|exists:tipos_cocina,id',
            'rango_precio_id' => 'sometimes|required|integer|exists:rangos_precio,id',
            'capacidad_total' => 'sometimes|required|integer|min:1',
            
            // Horarios
            'horarios' => 'nullable|json',
            
            // Relaciones
            'persona_juridica_id' => 'nullable|integer|exists:core_personas_juridicas,id',
            'grupo_empresarial_id' => 'nullable|integer|exists:grupos_empresariales,id',
            
            // Estado
            'activo' => 'boolean',
            
            // Arrays
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
            'slug.unique' => 'Ya existe un restaurante con este slug',
            'email.email' => 'El email debe ser válido',
            'capacidad_total.min' => 'La capacidad debe ser al menos 1',
        ];
    }
}
