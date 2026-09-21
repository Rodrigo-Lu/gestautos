<?php

namespace App\Enums;

enum TipoReporte: string
{
    case VENTAS       = 'VENTAS';
    case INVENTARIO   = 'INVENTARIO';
    case COBRANZAS    = 'COBRANZAS';
    case RENTABILIDAD = 'RENTABILIDAD';

    public function etiqueta(): string
    {
        return match ($this) {
            self::VENTAS       => 'Ventas',
            self::INVENTARIO   => 'Inventario',
            self::COBRANZAS    => 'Cobranzas',
            self::RENTABILIDAD => 'Rentabilidad',
        };
    }

    /** Reportes que solo puede ver la direccion. */
    public function esSensible(): bool
    {
        return $this === self::RENTABILIDAD;
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $t) => [$t->value => $t->etiqueta()]
        )->all();
    }
}
