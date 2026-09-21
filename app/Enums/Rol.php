<?php

namespace App\Enums;

enum Rol: string
{
    case PROPIETARIO   = 'PROPIETARIO';
    case ADMINISTRADOR = 'ADMINISTRADOR';
    case EMPLEADO      = 'EMPLEADO';
    case CLIENTE       = 'CLIENTE';

    public function etiqueta(): string
    {
        return match ($this) {
            self::PROPIETARIO   => 'Propietario',
            self::ADMINISTRADOR => 'Administrador',
            self::EMPLEADO      => 'Empleado',
            self::CLIENTE       => 'Cliente',
        };
    }

    /** Roles que acceden al panel de gestion. */
    public static function internos(): array
    {
        return [self::PROPIETARIO, self::ADMINISTRADOR, self::EMPLEADO];
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $r) => [$r->value => $r->etiqueta()]
        )->all();
    }
}
