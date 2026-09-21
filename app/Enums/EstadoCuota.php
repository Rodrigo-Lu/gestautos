<?php

namespace App\Enums;

enum EstadoCuota: string
{
    case PENDIENTE = 'PENDIENTE';
    case PARCIAL   = 'PARCIAL';
    case PAGADA    = 'PAGADA';
    case VENCIDA   = 'VENCIDA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::PENDIENTE => 'Pendiente',
            self::PARCIAL   => 'Pago parcial',
            self::PAGADA    => 'Pagada',
            self::VENCIDA   => 'Vencida',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDIENTE => 'secondary',
            self::PARCIAL   => 'info',
            self::PAGADA    => 'success',
            self::VENCIDA   => 'danger',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $e) => [$e->value => $e->etiqueta()]
        )->all();
    }
}
