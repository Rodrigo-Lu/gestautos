<?php

namespace App\Services;

use App\Models\Cuota;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Calculo del plan de pagos en cuotas (sistema frances: cuota fija).
 *
 * Esta clase es pura: no toca la base de datos. La usan por igual el
 * registro de una venta financiada y el simulador publico, de modo que
 * el numero que ve el visitante en la web es exactamente el mismo que
 * despues figura en el contrato.
 */
class FinanciamientoService
{
    /**
     * Genera el cronograma completo.
     *
     * @param  float   $montoFinanciado  Precio menos anticipo y permuta.
     * @param  int     $cantidadCuotas   Cantidad de cuotas mensuales.
     * @param  float   $tasaMensual      Tasa de interes mensual en porcentaje (5 = 5%).
     * @param  Carbon  $primerVencimiento
     * @param  int     $decimales        0 para guaranies, 2 para dolares.
     *
     * @return array{valor_cuota: float, total: float, total_interes: float, cuotas: array<int, array<string, mixed>>}
     */
    public function generarPlan(
        float $montoFinanciado,
        int $cantidadCuotas,
        float $tasaMensual,
        Carbon $primerVencimiento,
        int $decimales = 0
    ): array {
        if ($montoFinanciado <= 0) {
            throw new \InvalidArgumentException('El monto a financiar debe ser mayor a cero.');
        }

        if ($cantidadCuotas < 1) {
            throw new \InvalidArgumentException('La cantidad de cuotas debe ser al menos 1.');
        }

        $i = $tasaMensual / 100;

        $valorCuota = $i > 0
            ? $montoFinanciado * $i / (1 - pow(1 + $i, -$cantidadCuotas))
            : $montoFinanciado / $cantidadCuotas;

        $valorCuota = round($valorCuota, $decimales);

        $cuotas = [];
        $saldo  = $montoFinanciado;
        $total  = 0.0;
        $totalInteres = 0.0;

        for ($n = 1; $n <= $cantidadCuotas; $n++) {
            $interes = round($saldo * $i, $decimales);

            if ($n === $cantidadCuotas) {
                // La ultima cuota absorbe el residuo del redondeo para que
                // la suma de capitales sea exactamente el monto financiado.
                $capital = round($saldo, $decimales);
                $montoCuota = round($capital + $interes, $decimales);
            } else {
                $capital = round($valorCuota - $interes, $decimales);
                $montoCuota = $valorCuota;
            }

            $saldo = round($saldo - $capital, $decimales);
            $total += $montoCuota;
            $totalInteres += $interes;

            $cuotas[] = [
                'numero_cuota'      => $n,
                'monto_cuota'       => $montoCuota,
                'monto_capital'     => $capital,
                'monto_interes'     => $interes,
                'fecha_vencimiento' => $primerVencimiento->copy()->addMonthsNoOverflow($n - 1),
            ];
        }

        return [
            'valor_cuota'   => $valorCuota,
            'total'         => round($total, $decimales),
            'total_interes' => round($totalInteres, $decimales),
            'cuotas'        => $cuotas,
        ];
    }

    /**
     * Version reducida para el simulador publico: solo los totales.
     *
     * @return array{valor_cuota: float, total: float, total_interes: float, monto_financiado: float}
     */
    public function simular(
        float $precio,
        float $anticipo,
        int $cantidadCuotas,
        float $tasaMensual,
        int $decimales = 0
    ): array {
        $montoFinanciado = max($precio - $anticipo, 0);

        if ($montoFinanciado <= 0) {
            return [
                'valor_cuota'      => 0.0,
                'total'            => 0.0,
                'total_interes'    => 0.0,
                'monto_financiado' => 0.0,
            ];
        }

        $plan = $this->generarPlan(
            $montoFinanciado,
            $cantidadCuotas,
            $tasaMensual,
            Carbon::today()->addMonthNoOverflow(),
            $decimales
        );

        return [
            'valor_cuota'      => $plan['valor_cuota'],
            'total'            => $plan['total'],
            'total_interes'    => $plan['total_interes'],
            'monto_financiado' => $montoFinanciado,
        ];
    }

    /**
     * Mora acumulada de una cuota atrasada, calculada al dia de hoy.
     */
    public function calcularMora(Cuota $cuota): float
    {
        $dias = $cuota->dias_atraso;

        if ($dias <= 0 || $cuota->saldo <= 0) {
            return 0.0;
        }

        $tasaDiaria = (float) $cuota->financiamiento->tasa_mora_diaria / 100;

        return round($cuota->saldo * $tasaDiaria * $dias, 2);
    }

    /**
     * Comparativo de planes para mostrar al cliente en el mostrador.
     *
     * @param  array<int>  $plazos
     */
    public function comparar(float $precio, float $anticipo, array $plazos, float $tasaMensual, int $decimales = 0): Collection
    {
        return collect($plazos)->map(function (int $n) use ($precio, $anticipo, $tasaMensual, $decimales) {
            $r = $this->simular($precio, $anticipo, $n, $tasaMensual, $decimales);
            $r['cantidad_cuotas'] = $n;

            return $r;
        });
    }
}
