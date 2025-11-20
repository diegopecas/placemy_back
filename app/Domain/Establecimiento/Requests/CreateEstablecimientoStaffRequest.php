<?php

namespace App\Domain\Establecimiento\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEstablecimientoStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasPermission('restaurante_staff.asignar');
    }

    public function rules(): array
    {
        return [
            'establecimiento_id' => 'required|integer|exists:establecimientos,id',
            'cargo_id' => 'required|integer|exists:cargos,id',
            'usuario_id' => 'required|integer|exists:core_usuarios,id',
            'codigo_empleado' => 'required|string|max:20',
            'fecha_asignacion' => 'nullable|date',
            'activo' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'establecimiento_id.required' => 'El establecimiento es obligatorio',
            'establecimiento_id.exists' => 'El establecimiento seleccionado no existe',
            'cargo_id.required' => 'El cargo es obligatorio',
            'cargo_id.exists' => 'El cargo seleccionado no existe',
            'usuario_id.required' => 'El usuario es obligatorio',
            'usuario_id.exists' => 'El usuario seleccionado no existe',
            'codigo_empleado.required' => 'El código de empleado es obligatorio',
            'codigo_empleado.max' => 'El código de empleado no puede tener más de 20 caracteres',
            'fecha_asignacion.date' => 'La fecha de asignación debe ser una fecha válida',
        ];
    }
}