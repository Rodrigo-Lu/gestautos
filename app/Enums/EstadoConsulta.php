<?php

namespace App\Enums;

enum EstadoConsulta: string
{
    case NUEVA      = 'NUEVA';
    case CONTACTADA = 'CONTACTADA';
    case CERRADA    = 'CERRADA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::NUEVA      => 'Nueva',
            self::CONTACTADA => 'Contactada',
            self::CERRADA    => 'Cerrada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::NUEVA      => 'danger',
            self::CONTACTADA => 'warning',
            self::CERRADA    => 'secondary',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $e) => [$e->value => $e->etiqueta()]
        )->all();
    }
}
