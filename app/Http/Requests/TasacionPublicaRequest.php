<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TasacionPublicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_contacto'   => ['required', 'string', 'max:120'],
            'telefono_contacto' => ['required', 'string', 'max:30'],
            'marca'             => ['required', 'string', 'max:60'],
            'modelo'            => ['required', 'string', 'max:60'],
            'anio'              => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'kilometraje'       => ['required', 'integer', 'min:0', 'max:2000000'],
            'observaciones'     => ['nullable', 'string', 'max:1000'],
        ];
    }
}
