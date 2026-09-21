<?php

namespace App\Services;

use App\Enums\EstadoCuota;
use App\Enums\EstadoVehiculo;
use App\Enums\EstadoVenta;
use App\Models\Cuota;
use App\Models\Vehiculo;
use App\Models\Venta;
use Illuminate\Support\Collection;

/**
 * Consultas de los cuatro reportes del sistema.
 * Devuelve datos crudos: la vista decide como mostrarlos.
 */
class ReporteService
{
    /** @return array<string, mixed> */
    public function ventas(?string $desde, ?string $hasta): array
    {
        $ventas = Venta::with(['cliente', 'vehiculo', 'vendedor'])
            ->vigentes()
            ->entre($desde, $hasta)
            ->orderByDesc('fecha')
            ->get();

        return [
            'ventas'        => $ventas,
            'cantidad'      => $ventas->count(),
            'total_gs'      => $ventas->sum(fn (Venta $v) => $v->monto_en_guaranies),
            'por_vendedor'  => $ventas->groupBy(fn (Venta $v) => $v->vendedor->nombre)
                                      ->map(fn (Collection $g) => [
                                          'cantidad' => $g->count(),
                                          'total_gs' => $g->sum(fn (Venta $v) => $v->monto_en_guaranies),
                                      ]),
            'por_tipo'      => $ventas->groupBy(fn (Venta $v) => $v->tipo_venta->etiqueta())
                                      ->map(fn (Collection $g) => $g->count()),
        ];
    }

    /** @return array<string, mixed> */
    public function inventario(): array
    {
        $vehiculos = Vehiculo::with('fotoPrincipal')
            ->where('activo', true)
            ->orderBy('estado')
            ->orderBy('marca')
            ->get();

        return [
            'vehiculos'   => $vehiculos,
            'por_estado'  => $vehiculos->groupBy(fn (Vehiculo $v) => $v->estado->etiqueta())
                                       ->map(fn (Collection $g) => $g->count()),
            'por_marca'   => $vehiculos->groupBy('marca')->map(fn (Collection $g) => $g->count())->sortDesc(),
            'disponibles' => $vehiculos->where('estado', EstadoVehiculo::DISPONIBLE)->count(),
            'antiguedad'  => $vehiculos->where('estado', EstadoVehiculo::DISPONIBLE)
                                       ->map(fn (Vehiculo $v) => $v->fecha_ingreso->diffInDays(now()))
                                       ->avg(),
        ];
    }

    /** @return array<string, mixed> */
    public function cobranzas(?string $desde, ?string $hasta): array
    {
        $cuotas = Cuota::with('financiamiento.venta.cliente')
            ->when($desde, fn ($q) => $q->whereDate('fecha_vencimiento', '>=', $desde))
            ->when($hasta, fn ($q) => $q->whereDate('fecha_vencimiento', '<=', $hasta))
            ->orderBy('fecha_vencimiento')
            ->get();

        return [
            'cuotas'         => $cuotas,
            'total_cobrado'  => $cuotas->sum('monto_pagado'),
            'total_pendiente'=> $cuotas->sum(fn (Cuota $c) => $c->saldo),
            'total_mora'     => $cuotas->sum('monto_mora'),
            'vencidas'       => $cuotas->where('estado', EstadoCuota::VENCIDA)->count(),
            'pagadas'        => $cuotas->where('estado', EstadoCuota::PAGADA)->count(),
        ];
    }

    /**
     * Solo para propietario y administrador: cruza precio de compra
     * contra precio de venta.
     *
     * @return array<string, mixed>
     */
    public function rentabilidad(?string $desde, ?string $hasta): array
    {
        $ventas = Venta::with(['vehiculo', 'financiamiento'])
            ->where('estado', '!=', EstadoVenta::ANULADA->value)
            ->entre($desde, $hasta)
            ->get();

        $filas = $ventas->map(function (Venta $v) {
            $compra   = (float) ($v->vehiculo->precio_compra ?? 0);
            $venta    = (float) $v->precio_venta;
            $interes  = (float) ($v->financiamiento?->monto_total_con_interes ?? 0)
                      - (float) ($v->financiamiento?->monto_financiado ?? 0);
            $margen   = $venta - $compra + $interes;

            return [
                'venta'           => $v,
                'precio_compra'   => $compra,
                'precio_venta'    => $venta,
                'interes'         => $interes,
                'margen'          => $margen,
                'margen_pct'      => $compra > 0 ? round($margen / $compra * 100, 1) : null,
            ];
        });

        return [
            'filas'         => $filas,
            'margen_total'  => $filas->sum('margen'),
            'interes_total' => $filas->sum('interes'),
            'sin_costo'     => $filas->where('precio_compra', 0)->count(),
        ];
    }
}
