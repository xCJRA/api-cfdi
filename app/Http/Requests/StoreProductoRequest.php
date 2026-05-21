<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string|max:255',
            'clave_sat'     => 'required|string|max:8',
            'unidad_medida' => 'required|string|max:3',
            'precio'        => 'required|numeric|min:0',
            'iva_aplicable' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'        => 'El nombre del producto es obligatorio.',
            'clave_sat.required'     => 'La clave SAT es obligatoria.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'precio.required'        => 'El precio es obligatorio.',
            'precio.numeric'         => 'El precio debe ser un número válido.',
            'precio.min'             => 'El precio no puede ser negativo.',
            'iva_aplicable.required' => 'Debes indicar si aplica IVA.',
            'iva_aplicable.boolean'  => 'El campo IVA aplicable debe ser verdadero o falso.',
        ];
    }
}
