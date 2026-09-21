<?php

namespace Database\Factories;

use App\Enums\Rol;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'nombre'            => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= 'gestautos2026',
            'rol'               => Rol::EMPLEADO,
            'activo'            => true,
            'remember_token'    => Str::random(10),
        ];
    }

    public function rol(Rol $rol): static
    {
        return $this->state(fn () => ['rol' => $rol]);
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['activo' => false]);
    }
}
