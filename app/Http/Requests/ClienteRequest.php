<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esInterno() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('cliente')?->id;

        return [
            'nombre'    => ['required', 'string', 'max:120'],
            'cedula'    => ['required', 'string', 'max:25', Rule::unique('clientes')->ignore($id)],
            'telefono'  => ['required', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:180'],
            'email'     => ['nullable', 'email', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'cedula.unique' => 'Ya hay un cliente cargado con esa cédula.',
        ];
    }
}
