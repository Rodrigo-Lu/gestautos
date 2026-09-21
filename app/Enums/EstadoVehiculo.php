<?php

namespace App\Enums;

enum EstadoVehiculo: string
{
    case DISPONIBLE = 'DISPONIBLE';
    case RESERVADO  = 'RESERVADO';
    case VENDIDO    = 'VENDIDO';

    public function etiqueta(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'Disponible',
            self::RESERVADO  => 'Reservado',
            self::VENDIDO    => 'Vendido',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DISPONIBLE => 'success',
            self::RESERVADO  => 'warning',
            self::VENDIDO    => 'secondary',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $e) => [$e->value => $e->etiqueta()]
        )->all();
    }
}
