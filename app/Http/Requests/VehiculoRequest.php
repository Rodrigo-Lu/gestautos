<?php

namespace App\Http\Requests;

use App\Enums\EstadoVehiculo;
use App\Enums\Moneda;
use App\Enums\TipoCombustible;
use App\Enums\TipoTransmision;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class VehiculoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esInterno() ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('vehiculo')?->id;

        return [
            'codigo_publicacion' => ['required', 'string', 'max:30', Rule::unique('vehiculos')->ignore($id)],
            'marca'              => ['required', 'string', 'max:60'],
            'modelo'             => ['required', 'string', 'max:60'],
            'version'            => ['nullable', 'string', 'max:60'],
            'anio'               => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'precio'             => ['required', 'numeric', 'min:0'],
            'precio_compra'      => ['nullable', 'numeric', 'min:0', 'lte:precio'],
            'moneda'             => ['required', new Enum(Moneda::class)],
            'kilometraje'        => ['required', 'integer', 'min:0', 'max:2000000'],
            'color'              => ['nullable', 'string', 'max:40'],
            'numero_chasis'      => ['nullable', 'string', 'max:40', Rule::unique('vehiculos')->ignore($id)],
            'transmision'        => ['required', new Enum(TipoTransmision::class)],
            'combustible'        => ['required', new Enum(TipoCombustible::class)],
            'acepta_permuta'     => ['boolean'],
            'fecha_ingreso'      => ['required', 'date', 'before_or_equal:today'],
            'estado'             => ['required', new Enum(EstadoVehiculo::class)],
            'activo'             => ['boolean'],
            'descripcion'        => ['nullable', 'string', 'max:2000'],
            'fotos'              => ['nullable', 'array', 'max:'.config('gestautos.catalogo.fotos_por_vehiculo')],
            'fotos.*'            => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'acepta_permuta' => $this->boolean('acepta_permuta'),
            'activo'         => $this->boolean('activo', true),
        ]);
    }

    public function messages(): array
    {
        return [
            'codigo_publicacion.unique' => 'Ya existe otro vehículo con ese código de publicación.',
            'numero_chasis.unique'      => 'Ese número de chasis ya está cargado en otro vehículo.',
            'precio_compra.lte'         => 'El precio de compra no puede ser mayor al precio de venta.',
            'fotos.*.max'               => 'Cada foto puede pesar hasta 4 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo_publicacion' => 'código de publicación',
            'anio'               => 'año',
            'fecha_ingreso'      => 'fecha de ingreso',
        ];
    }
}
