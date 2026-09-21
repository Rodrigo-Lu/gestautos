<?php

namespace App\Enums;

enum EstadoTasacion: string
{
    case PENDIENTE  = 'PENDIENTE';
    case EVALUADA   = 'EVALUADA';
    case DESCARTADA = 'DESCARTADA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::PENDIENTE  => 'Pendiente',
            self::EVALUADA   => 'Evaluada',
            self::DESCARTADA => 'Descartada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDIENTE  => 'warning',
            self::EVALUADA   => 'success',
            self::DESCARTADA => 'secondary',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $e) => [$e->value => $e->etiqueta()]
        )->all();
    }
}
