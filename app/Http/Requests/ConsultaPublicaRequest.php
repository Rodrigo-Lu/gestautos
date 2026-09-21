<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultaPublicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => ['required', 'string', 'max:120'],
            'telefono'    => ['required', 'string', 'max:30'],
            'email'       => ['nullable', 'email', 'max:120'],
            'mensaje'     => ['required', 'string', 'max:1000'],
            'vehiculo_id' => ['nullable', 'exists:vehiculos,id'],
        ];
    }
}
