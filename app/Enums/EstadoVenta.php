<?php

namespace App\Enums;

enum EstadoVenta: string
{
    case VIGENTE    = 'VIGENTE';
    case FINALIZADA = 'FINALIZADA';
    case ANULADA    = 'ANULADA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::VIGENTE    => 'Vigente',
            self::FINALIZADA => 'Finalizada',
            self::ANULADA    => 'Anulada',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::VIGENTE    => 'primary',
            self::FINALIZADA => 'success',
            self::ANULADA    => 'danger',
        };
    }

    public static function opciones(): array
    {
        return collect(self::cases())->mapWithKeys(
            fn (self $e) => [$e->value => $e->etiqueta()]
        )->all();
    }
}
