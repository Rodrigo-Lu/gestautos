<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ClienteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre'    => fake()->name(),
            'cedula'    => (string) fake()->unique()->numberBetween(700000, 6999999),
            'telefono'  => '09'.fake()->numberBetween(61, 99).fake()->numerify('######'),
            'direccion' => fake()->streetAddress().', San Lorenzo',
            'email'     => fake()->unique()->safeEmail(),
        ];
    }
}
