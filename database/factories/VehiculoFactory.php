<?php

namespace Database\Factories;

use App\Enums\EstadoVehiculo;
use App\Enums\Moneda;
use App\Enums\TipoCombustible;
use App\Enums\TipoTransmision;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehiculoFactory extends Factory
{
    public function definition(): array
    {
        $marcas = [
            'Toyota'    => ['Corolla', 'Hilux', 'RAV4', 'Yaris', 'Etios'],
            'Nissan'    => ['Frontier', 'Kicks', 'March', 'X-Trail'],
            'Chevrolet' => ['Onix', 'S10', 'Tracker', 'Spin'],
            'Volkswagen'=> ['Gol', 'Amarok', 'T-Cross', 'Polo'],
            'Kia'       => ['Sportage', 'Rio', 'Cerato', 'Sorento'],
            'Hyundai'   => ['Tucson', 'HB20', 'Creta'],
        ];

        $marca  = fake()->randomElement(array_keys($marcas));
        $modelo = fake()->randomElement($marcas[$marca]);
        $precio = fake()->numberBetween(45, 350) * 1_000_000;

        return [
            'codigo_publicacion' => 'JP-'.fake()->unique()->numerify('#####'),
            'marca'              => $marca,
            'modelo'             => $modelo,
            'version'            => fake()->randomElement(['XEI', 'GLI', 'Full', 'Base', 'Sport', null]),
            'anio'               => fake()->numberBetween(2012, 2025),
            'precio'             => $precio,
            'precio_compra'      => (int) ($precio * fake()->randomFloat(2, 0.72, 0.88)),
            'moneda'             => Moneda::GS,
            'kilometraje'        => fake()->numberBetween(5_000, 220_000),
            'color'              => fake()->randomElement(['Blanco', 'Negro', 'Gris', 'Plata', 'Rojo', 'Azul']),
            'numero_chasis'      => strtoupper(fake()->unique()->bothify('?#?#####??######')),
            'transmision'        => fake()->randomElement(TipoTransmision::cases()),
            'combustible'        => fake()->randomElement([
                TipoCombustible::NAFTA, TipoCombustible::NAFTA, TipoCombustible::DIESEL,
            ]),
            'acepta_permuta'     => fake()->boolean(70),
            'fecha_ingreso'      => fake()->dateTimeBetween('-8 months', 'now')->format('Y-m-d'),
            'activo'             => true,
            'estado'             => EstadoVehiculo::DISPONIBLE,
            'descripcion'        => 'Unico dueno, service al dia, papeles en regla.',
        ];
    }

    public function vendido(): static
    {
        return $this->state(fn () => ['estado' => EstadoVehiculo::VENDIDO]);
    }
}
