<?php

namespace App\Services;

use App\Enums\EstadoCuota;
use App\Enums\EstadoVenta;
use App\Enums\TipoPago;
use App\Models\Cuota;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Registro de pagos de cuotas, incluyendo pagos parciales y mora.
 */
class CobranzaService
{
    public function __construct(
        private FinanciamientoService $financiamiento,
        private ComprobanteService $comprobantes,
    ) {}

    public function registrarPago(Cuota $cuota, float $monto, TipoPago $formaPago, User $usuario): Cuota
    {
        return DB::transaction(function () use ($cuota, $monto, $formaPago, $usuario) {
            $cuota = Cuota::lockForUpdate()->findOrFail($cuota->id);

            if ($cuota->estaPagada()) {
                throw ValidationException::withMessages([
                    'monto' => 'Esa cuota ya esta pagada.',
                ]);
            }

            if ($monto <= 0) {
                throw ValidationException::withMessages([
                    'monto' => 'El monto a cobrar debe ser mayor a cero.',
                ]);
            }

            // La mora se congela al momento del cobro.
            $mora = $this->financiamiento->calcularMora($cuota);

            $pagadoTotal = round((float) $cuota->monto_pagado + $monto, 2);
            $cubreTodo   = $pagadoTotal >= (float) $cuota->monto_cuota;

            $cuota->update([
                'monto_pagado' => min($pagadoTotal, (float) $cuota->monto_cuota),
                'monto_mora'   => $mora,
                'estado'       => $cubreTodo ? EstadoCuota::PAGADA : EstadoCuota::PARCIAL,
                'fecha_pago'   => $cubreTodo ? now()->toDateString() : $cuota->fecha_pago,
                'forma_pago'   => $formaPago,
                'usuario_id'   => $usuario->id,
            ]);

            // Al quedar pagada, la alerta de esa cuota deja de tener sentido.
            $cuota->alertas()->update(['leida' => true]);

            $this->comprobantes->emitirPorCuota($cuota, $monto, $usuario);

            $this->cerrarVentaSiCorresponde($cuota);

            return $cuota->refresh();
        });
    }

    private function cerrarVentaSiCorresponde(Cuota $cuota): void
    {
        $financiamiento = $cuota->financiamiento->refresh();

        if ($financiamiento->estaCancelado()) {
            $financiamiento->venta->update(['estado' => EstadoVenta::FINALIZADA]);
        }
    }
}
