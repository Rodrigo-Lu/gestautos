<?php

namespace Database\Factories;

use App\Enums\EstadoTasacion;
use App\Enums\Moneda;
use Illuminate\Database\Eloquent\Factories\Factory;

class TasacionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre_contacto'   => fake()->name(),
            'telefono_contacto' => '09'.fake()->numberBetween(61, 99).fake()->numerify('######'),
            'marca'             => fake()->randomElement(['Toyota', 'Nissan', 'Chevrolet', 'Fiat', 'Ford']),
            'modelo'            => fake()->randomElement(['Corolla', 'March', 'Onix', 'Uno', 'Ka']),
            'anio'              => fake()->numberBetween(2008, 2022),
            'kilometraje'       => fake()->numberBetween(30_000, 260_000),
            'monto_ofrecido'    => null,
            'moneda'            => Moneda::GS,
            'fecha'             => fake()->dateTimeBetween('-3 months', 'now')->format('Y-m-d'),
            'estado'            => EstadoTasacion::PENDIENTE,
        ];
    }
}
