<?php

namespace App\Http\Controllers;

use App\Enums\EstadoCuota;
use App\Enums\EstadoVehiculo;
use App\Enums\EstadoVenta;
use App\Models\Cuota;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Services\FinanciamientoService;

class DashboardController extends Controller
{
    public function __invoke(FinanciamientoService $financiamiento)
    {
        $hoy = now();
        $inicioMes = $hoy->copy()->startOfMonth();
        $finMes = $hoy->copy()->endOfMonth();

        $ventasMes = Venta::vigentes()
            ->whereBetween('fecha', [$inicioMes->toDateString(), $finMes->toDateString()])
            ->get();

        $ventasMesPorMoneda = [
            'GS'  => 0.0,
            'USD' => 0.0,
        ];

        foreach ($ventasMes as $venta) {
            $ventasMesPorMoneda[$venta->moneda->value] += (float) $venta->precio_venta;
        }

        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
            $mes = $hoy->copy()->startOfMonth()->subMonths($i);
            $meses[$mes->format('Y-m')] = $mes->format('m/Y');
        }

        $ventasPorMes = [
            'GS'  => array_fill_keys(array_keys($meses), 0.0),
            'USD' => array_fill_keys(array_keys($meses), 0.0),
        ];

        Venta::vigentes()
            ->whereDate('fecha', '>=', array_key_first($meses).'-01')
            ->whereDate('fecha', '<=', $finMes->toDateString())
            ->get(['fecha', 'moneda', 'precio_venta'])
            ->each(function (Venta $venta) use (&$ventasPorMes) {
                $mes = $venta->fecha->format('Y-m');

                if (array_key_exists($mes, $ventasPorMes[$venta->moneda->value])) {
                    $ventasPorMes[$venta->moneda->value][$mes] += (float) $venta->precio_venta;
                }
            });

        $porCobrar = Cuota::query()
            ->join('financiamientos', 'financiamientos.id', '=', 'cuotas.financiamiento_id')
            ->join('ventas', 'ventas.id', '=', 'financiamientos.venta_id')
            ->whereIn('cuotas.estado', [
                EstadoCuota::PENDIENTE->value,
                EstadoCuota::PARCIAL->value,
                EstadoCuota::VENCIDA->value,
            ])
            ->whereIn('ventas.estado', [
                EstadoVenta::VIGENTE->value,
                EstadoVenta::FINALIZADA->value,
            ])
            ->whereNull('ventas.deleted_at')
            ->whereRaw('cuotas.monto_cuota > cuotas.monto_pagado')
            ->selectRaw('ventas.moneda, SUM(cuotas.monto_cuota - cuotas.monto_pagado) as total')
            ->groupBy('ventas.moneda')
            ->pluck('total', 'moneda')
            ->map(fn ($total) => (float) $total)
            ->all();

        $montoPorCobrar = [
            'GS'  => $porCobrar['GS'] ?? 0.0,
            'USD' => $porCobrar['USD'] ?? 0.0,
        ];

        $stock = Vehiculo::where('activo', true)
            ->selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $stockLabels = [];
        $stockData = [];
        $stockColors = [];

        foreach (EstadoVehiculo::cases() as $estado) {
            $stockLabels[] = $estado->etiqueta();
            $stockData[] = (int) ($stock[$estado->value] ?? 0);
            $stockColors[] = match ($estado) {
                EstadoVehiculo::DISPONIBLE => '#28a745',
                EstadoVehiculo::RESERVADO  => '#ffc107',
                EstadoVehiculo::VENDIDO    => '#6c757d',
            };
        }

        return view('dashboard', [
            'disponibles'        => Vehiculo::disponibles()->count(),
            'ventasMesCantidad'  => $ventasMes->count(),
            'ventasMesPorMoneda' => $ventasMesPorMoneda,
            'cuotasVencidas'     => Cuota::vencidas()->count(),
            'montoPorCobrar'     => $montoPorCobrar,
            'mesesLabels'        => array_values($meses),
            'ventasPorMesGs'     => array_values($ventasPorMes['GS']),
            'ventasPorMesUsd'    => array_values($ventasPorMes['USD']),
            'stockLabels'        => $stockLabels,
            'stockData'          => $stockData,
            'stockColors'        => $stockColors,
            'ultimasVentas'      => Venta::with(['cliente', 'vehiculo'])
                                        ->vigentes()
                                        ->latest('fecha')
                                        ->limit(5)
                                        ->get(),
            'proximasCuotas'     => Cuota::with('financiamiento.venta.cliente', 'financiamiento.venta.vehiculo')
                                        ->abiertas()
                                        ->whereDate('fecha_vencimiento', '>=', $hoy->toDateString())
                                        ->orderBy('fecha_vencimiento')
                                        ->limit(5)
                                        ->get()
                                        ->each(fn (Cuota $cuota) => $cuota->setAttribute(
                                            'mora_hoy',
                                            $financiamiento->calcularMora($cuota),
                                        )),
        ]);
    }
}
