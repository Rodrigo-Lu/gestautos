<?php

namespace App\Enums;

enum TipoComprobante: string
{
    case COMPROBANTE_VENTA = 'COMPROBANTE_VENTA';
    case RECIBO_PAGO_CUOTA = 'RECIBO_PAGO_CUOTA';

    public function etiqueta(): string
    {
        return match ($this) {
            self::COMPROBANTE_VENTA => 'Comprobante de venta',
            self::RECIBO_PAGO_CUOTA => 'Recibo de pago de cuota',
        };
    }

    public function prefijo(): string
    {
        return $this === self::COMPROBANTE_VENTA ? 'CV' : 'RC';
    }
}
