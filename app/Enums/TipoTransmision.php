<?php

namespace App\Enums;

enum TipoTransmision: string
{
    case MANUAL     = 'MANUAL';
    case AUTOMATICA = 'AUTOMATICA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::MANUAL     => 'Manual',
            self::AUTOMATICA => 'Automatica',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $t) => [$t->value => $t->etiqueta()]
        )->all();
    }
}
