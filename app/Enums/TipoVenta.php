<?php

namespace App\Enums;

enum TipoVenta: string
{
    case CONTADO    = 'CONTADO';
    case FINANCIADO = 'FINANCIADO';

    public function etiqueta(): string
    {
        return match ($this) {
            self::CONTADO    => 'Contado',
            self::FINANCIADO => 'Financiado',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $t) => [$t->value => $t->etiqueta()]
        )->all();
    }
}
