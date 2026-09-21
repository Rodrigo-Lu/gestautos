<?php

namespace App\Enums;

enum TipoPago: string
{
    case EFECTIVO      = 'EFECTIVO';
    case TRANSFERENCIA = 'TRANSFERENCIA';
    case TARJETA       = 'TARJETA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::EFECTIVO      => 'Efectivo',
            self::TRANSFERENCIA => 'Transferencia',
            self::TARJETA       => 'Tarjeta',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $t) => [$t->value => $t->etiqueta()]
        )->all();
    }
}
