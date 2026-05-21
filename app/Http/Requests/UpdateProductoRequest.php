<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'        => 'sometimes|string|max:255',
            'descripcion'   => 'nullable|string|max:255',
            'clave_sat'     => 'sometimes|string|max:8',
            'unidad_medida' => 'sometimes|string|max:3',
            'precio'        => 'sometimes|numeric|min:0',
            'iva_aplicable' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'precio.numeric'        => 'El precio debe ser un número válido.',
            'precio.min'            => 'El precio no puede ser negativo.',
            'iva_aplicable.boolean' => 'El campo IVA aplicable debe ser verdadero o falso.',
        ];
    }
}
