<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'         => 'required|string|max:255',
            'rfc'            => ['required', 'string', 'size:12,13', 'unique:clientes,rfc', 'regex:/^[A-ZÑ&]{3,4}\d{6}[A-Z0-9]{3}$/'],
            'email'          => 'required|email|unique:clientes,email',
            'telefono'       => 'nullable|string|max:15',
            'direccion'      => 'nullable|string|max:255',
            'regimen_fiscal' => 'nullable|string|size:3',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'         => 'El nombre es obligatorio.',
            'rfc.required'            => 'El RFC es obligatorio.',
            'rfc.regex'               => 'El RFC no tiene un formato válido.',
            'rfc.unique'              => 'Ya existe un cliente con este RFC.',
            'email.required'          => 'El email es obligatorio.',
            'email.email'             => 'El email no tiene un formato válido.',
            'email.unique'            => 'Ya existe un cliente con este email.',
        ];
    }
}
