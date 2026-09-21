<?php

namespace App\Http\Requests;

use App\Enums\TipoPago;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CobroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esInterno() ?? false;
    }

    public function rules(): array
    {
        return [
            'monto'      => ['required', 'numeric', 'min:1'],
            'forma_pago' => ['required', new Enum(TipoPago::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'monto.min' => 'El monto cobrado debe ser mayor a cero.',
        ];
    }
}
