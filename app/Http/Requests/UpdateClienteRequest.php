<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cliente = $this->route('cliente');

        return [
            'nombre' => 'sometimes|string|max:255',

            'rfc' => [
                'sometimes',
                'string',
                'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/',
                Rule::unique('clientes', 'rfc')->ignore($cliente),
            ],

            'email' => [
                'sometimes',
                'email',
                Rule::unique('clientes', 'email')->ignore($cliente),
            ],

            'telefono' => 'nullable|string|max:15',
            'direccion' => 'nullable|string|max:255',
            'regimen_fiscal' => 'nullable|string|size:3',
        ];
    }

    public function messages(): array
    {
        return [
            'rfc.regex' => 'El RFC no tiene un formato válido.',
            'rfc.unique' => 'Ya existe un cliente con este RFC.',
            'email.unique' => 'Ya existe un cliente con este email.',
        ];
    }
}
