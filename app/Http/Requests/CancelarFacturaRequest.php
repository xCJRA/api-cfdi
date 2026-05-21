<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelarFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'motivo' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'El motivo de cancelación es obligatorio.',
        ];
    }
}
