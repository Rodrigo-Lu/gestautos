<?php

namespace App\Services;

use App\Enums\EstadoTasacion;
use App\Enums\EstadoVehiculo;
use App\Enums\EstadoVenta;
use App\Enums\TipoVenta;
use App\Models\Financiamiento;
use App\Models\Tasacion;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Registro y anulacion de ventas. Toda la operacion va dentro de una
 * transaccion: o se guarda la venta con su financiamiento, sus cuotas y
 * el cambio de estado del vehiculo, o no se guarda nada.
 */
class VentaService
{
    public function __construct(
        private FinanciamientoService $financiamiento,
        private ComprobanteService $comprobantes,
    ) {}

    /**
     * @param  array<string, mixed>  $datos
     */
    public function registrar(array $datos, User $usuario): Venta
    {
        return DB::transaction(function () use ($datos, $usuario) {
            /** @var Vehiculo $vehiculo */
            $vehiculo = Vehiculo::lockForUpdate()->findOrFail($datos['vehiculo_id']);

            if (! $vehiculo->puedeVenderse()) {
                throw ValidationException::withMessages([
                    'vehiculo_id' => 'Ese vehículo ya no está disponible para la venta.',
                ]);
            }

            $venta = Venta::create([
                'vehiculo_id'         => $vehiculo->id,
                'cliente_id'          => $datos['cliente_id'],
                'usuario_id'          => $usuario->id,
                'tasacion_permuta_id' => $datos['tasacion_permuta_id'] ?? null,
                'fecha'               => $datos['fecha'] ?? now()->toDateString(),
                'precio_venta'        => $datos['precio_venta'],
                'monto_permuta'       => $datos['monto_permuta'] ?? 0,
                'moneda'              => $datos['moneda'] ?? $vehiculo->moneda->value,
                'tipo_cambio'         => $datos['tipo_cambio'] ?? null,
                'tipo_venta'          => $datos['tipo_venta'],
                'forma_pago_anticipo' => $datos['forma_pago_anticipo'] ?? null,
                'estado'              => EstadoVenta::VIGENTE,
                'observaciones'       => $datos['observaciones'] ?? null,
            ]);

            if ($datos['tipo_venta'] === TipoVenta::FINANCIADO->value) {
                $this->crearFinanciamiento($venta, $datos);
            } else {
                $venta->update(['estado' => EstadoVenta::FINALIZADA]);
            }

            $vehiculo->update(['estado' => EstadoVehiculo::VENDIDO]);

            if ($venta->tasacion_permuta_id) {
                Tasacion::whereKey($venta->tasacion_permuta_id)
                    ->update(['estado' => EstadoTasacion::EVALUADA->value]);
            }

            $this->comprobantes->emitirPorVenta($venta, $usuario);

            return $venta->refresh();
        });
    }

    /**
     * @param  array<string, mixed>  $datos
     */
    private function crearFinanciamiento(Venta $venta, array $datos): Financiamiento
    {
        $anticipo = (float) ($datos['monto_anticipo'] ?? 0);
        $permuta  = (float) ($datos['monto_permuta'] ?? 0);
        $aFinanciar = (float) $datos['precio_venta'] - $anticipo - $permuta;

        if ($aFinanciar <= 0) {
            throw ValidationException::withMessages([
                'monto_anticipo' => 'El anticipo y la permuta ya cubren el precio: la venta seria al contado.',
            ]);
        }

        $decimales = $venta->moneda->decimales();

        $plan = $this->financiamiento->generarPlan(
            montoFinanciado: $aFinanciar,
            cantidadCuotas: (int) $datos['cantidad_cuotas'],
            tasaMensual: (float) ($datos['tasa_interes_mensual'] ?? 0),
            primerVencimiento: Carbon::parse($datos['fecha_primer_vencimiento']),
            decimales: $decimales,
        );

        $financiamiento = Financiamiento::create([
            'venta_id'                 => $venta->id,
            'monto_anticipo'           => $anticipo,
            'monto_financiado'         => $aFinanciar,
            'monto_total_con_interes'  => $plan['total'],
            'cantidad_cuotas'          => (int) $datos['cantidad_cuotas'],
            'tasa_interes_mensual'     => (float) ($datos['tasa_interes_mensual'] ?? 0),
            'tasa_mora_diaria'         => (float) ($datos['tasa_mora_diaria'] ?? 0),
            'fecha_primer_vencimiento' => $datos['fecha_primer_vencimiento'],
        ]);

        $financiamiento->cuotas()->createMany(
            collect($plan['cuotas'])->map(fn (array $c) => [
                'numero_cuota'      => $c['numero_cuota'],
                'monto_cuota'       => $c['monto_cuota'],
                'monto_capital'     => $c['monto_capital'],
                'monto_interes'     => $c['monto_interes'],
                'fecha_vencimiento' => $c['fecha_vencimiento']->toDateString(),
            ])->all()
        );

        return $financiamiento;
    }

    public function anular(Venta $venta, string $motivo, User $usuario): Venta
    {
        return DB::transaction(function () use ($venta, $motivo) {
            if (! $venta->puedeAnularse()) {
                throw ValidationException::withMessages([
                    'motivo_anulacion' => 'La venta ya estaba anulada.',
                ]);
            }

            $pagado = (float) ($venta->financiamiento?->total_pagado ?? 0);

            if ($pagado > 0) {
                throw ValidationException::withMessages([
                    'motivo_anulacion' => 'No se puede anular: la venta tiene cuotas cobradas. Registra primero la devolucion.',
                ]);
            }

            $venta->update([
                'estado'           => EstadoVenta::ANULADA,
                'motivo_anulacion' => $motivo,
            ]);

            $venta->vehiculo->update(['estado' => EstadoVehiculo::DISPONIBLE]);

            return $venta->refresh();
        });
    }
}
