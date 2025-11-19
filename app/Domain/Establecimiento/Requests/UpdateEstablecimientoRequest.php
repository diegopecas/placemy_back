<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstablecimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->hasPermission('establecimientos.editar');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $establecimientoId = $this->route('id');
        
        return [
            // Datos básicos
            'nombre' => 'sometimes|required|string|max:255',
            'slug' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('establecimientos', 'slug')->ignore($establecimientoId)
            ],
            'descripcion' => 'nullable|string|max:1000',
            
            // Ubicación
            'direccion' => 'sometimes|required|string|max:500',
            'ciudad_id' => 'sometimes|required|integer|exists:core_ciudades,id',
            
            // Contacto
            'telefono' => 'sometimes|required|string|max:20',
            'email_contacto' => 'nullable|email|max:150',
            
            // Información comercial
            'rango_precio_id' => 'nullable|integer|exists:rangos_precio,id',
            'capacidad_total' => 'nullable|integer|min:0',
            
            // Horarios
            'horario_apertura' => 'nullable|json',
            
            // URLs
            'logo_url' => 'nullable|url|max:500',
            'banner_url' => 'nullable|url|max:500',
            'url_menu' => 'nullable|url|max:500',
            
            // Relaciones
            'persona_juridica_id' => 'sometimes|required|integer|exists:core_personas_juridicas,id',
            
            // Estado
            'activo' => 'boolean',
            'verificado' => 'boolean',
            'fecha_apertura' => 'nullable|date',
            
            // Arrays
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
            'slug.unique' => 'Ya existe un establecimiento con este slug',
            'email_contacto.email' => 'El email debe ser válido',
            'capacidad_total.min' => 'La capacidad debe ser al menos 0',
        ];
    }
}
