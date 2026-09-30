<?php

namespace App\Enums;

enum TipoAlerta: string
{
    case POR_VENCER = 'POR_VENCER';
    case VENCE_HOY  = 'VENCE_HOY';
    case VENCIDA    = 'VENCIDA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::POR_VENCER => 'Por vencer',
            self::VENCE_HOY  => 'Vence hoy',
            self::VENCIDA    => 'Vencida',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::VENCIDA, self::VENCE_HOY => 'danger',
            self::POR_VENCER => 'warning',
        };
    }
}
