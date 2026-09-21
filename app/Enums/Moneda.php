<?php

namespace App\Enums;

enum Moneda: string
{
    case GS  = 'GS';
    case USD = 'USD';

    public function etiqueta(): string
    {
        return match ($this) {
            self::GS  => 'Guaranies',
            self::USD => 'Dolares',
        };
    }

    public function simbolo(): string
    {
        return match ($this) {
            self::GS  => 'Gs.',
            self::USD => 'US$',
        };
    }

    /** Decimales con los que se redondea esta moneda. */
    public function decimales(): int
    {
        return $this === self::GS ? 0 : 2;
    }

    public function formatear(float|string $monto): string
    {
        return $this->simbolo().' '.number_format((float) $monto, $this->decimales(), ',', '.');
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $m) => [$m->value => $m->etiqueta()]
        )->all();
    }
}
