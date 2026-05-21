<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'                => 'required|exists:clientes,id',
            'conceptos'                 => 'required|array|min:1',
            'conceptos.*.producto_id'   => 'required|exists:productos,id',
            'conceptos.*.cantidad'      => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required'              => 'El cliente es obligatorio.',
            'cliente_id.exists'                => 'El cliente seleccionado no existe.',
            'conceptos.required'               => 'Debes agregar al menos un concepto.',
            'conceptos.min'                    => 'Debes agregar al menos un concepto.',
            'conceptos.*.producto_id.required' => 'Cada concepto debe tener un producto.',
            'conceptos.*.producto_id.exists'   => 'Uno de los productos no existe.',
            'conceptos.*.cantidad.required'    => 'Cada concepto debe tener una cantidad.',
            'conceptos.*.cantidad.min'         => 'La cantidad mínima es 1.',
        ];
    }
}
