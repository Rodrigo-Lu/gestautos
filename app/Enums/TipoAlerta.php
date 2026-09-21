<?php

namespace App\Enums;

enum TipoAlerta: string
{
    case POR_VENCER = 'POR_VENCER';
    case VENCIDA    = 'VENCIDA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::POR_VENCER => 'Por vencer',
            self::VENCIDA    => 'Vencida',
        };
    }

    public function color(): string
    {
        return $this === self::VENCIDA ? 'danger' : 'warning';
    }
}
