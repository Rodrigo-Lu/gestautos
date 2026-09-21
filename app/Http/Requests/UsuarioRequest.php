<?php

namespace App\Http\Requests;

use App\Enums\Rol;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Rules\Password;

class UsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esAdministrativo() ?? false;
    }

    public function rules(): array
    {
        $id      = $this->route('usuario')?->id;
        $esAlta  = $this->isMethod('post');

        return [
            'nombre'   => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email', 'max:120', Rule::unique('users')->ignore($id)],
            'rol'      => ['required', new Enum(Rol::class)],
            'activo'   => ['boolean'],
            'password' => [$esAlta ? 'required' : 'nullable', 'confirmed', Password::min(8)],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo', true)]);
    }
}
