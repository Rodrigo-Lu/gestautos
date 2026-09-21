<?php

namespace App\Http\Requests;

use App\Enums\Moneda;
use App\Enums\TipoPago;
use App\Enums\TipoVenta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class VentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esInterno() ?? false;
    }

    public function rules(): array
    {
        $financiada = $this->input('tipo_venta') === TipoVenta::FINANCIADO->value;

        return [
            'vehiculo_id'         => ['required', 'exists:vehiculos,id'],
            'cliente_id'          => ['required', 'exists:clientes,id'],
            'fecha'               => ['required', 'date', 'before_or_equal:today'],
            'precio_venta'        => ['required', 'numeric', 'min:1'],
            'moneda'              => ['required', new Enum(Moneda::class)],
            'tipo_cambio'         => ['nullable', 'required_if:moneda,USD', 'numeric', 'min:1'],
            'tipo_venta'          => ['required', new Enum(TipoVenta::class)],
            'forma_pago_anticipo' => ['nullable', new Enum(TipoPago::class)],
            'tasacion_permuta_id' => ['nullable', 'exists:tasaciones,id'],
            'monto_permuta'       => ['nullable', 'numeric', 'min:0', 'lt:precio_venta'],
            'observaciones'       => ['nullable', 'string', 'max:1000'],

            'monto_anticipo'           => [$financiada ? 'required' : 'nullable', 'numeric', 'min:0'],
            'cantidad_cuotas'          => [$financiada ? 'required' : 'nullable', 'integer', 'min:1', 'max:72'],
            'tasa_interes_mensual'     => [$financiada ? 'required' : 'nullable', 'numeric', 'min:0', 'max:20'],
            'tasa_mora_diaria'         => ['nullable', 'numeric', 'min:0', 'max:5'],
            'fecha_primer_vencimiento' => [$financiada ? 'required' : 'nullable', 'date', 'after:fecha'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_cambio.required_if'            => 'Si la venta es en dólares, cargá la cotización del día.',
            'monto_permuta.lt'                   => 'La permuta no puede cubrir el precio completo.',
            'fecha_primer_vencimiento.after'     => 'El primer vencimiento tiene que ser posterior a la fecha de venta.',
            'cantidad_cuotas.required'           => 'Indicá en cuántas cuotas se financia.',
        ];
    }
}
