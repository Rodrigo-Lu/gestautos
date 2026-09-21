<?php

namespace App\Enums;

enum TipoCombustible: string
{
    case NAFTA    = 'NAFTA';
    case DIESEL   = 'DIESEL';
    case HIBRIDO  = 'HIBRIDO';
    case ELECTRICO = 'ELECTRICO';
    case GNC      = 'GNC';

    public function etiqueta(): string
    {
        return match ($this) {
            self::NAFTA     => 'Nafta',
            self::DIESEL    => 'Diesel',
            self::HIBRIDO   => 'Hibrido',
            self::ELECTRICO => 'Electrico',
            self::GNC       => 'GNC',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $t) => [$t->value => $t->etiqueta()]
        )->all();
    }
}
