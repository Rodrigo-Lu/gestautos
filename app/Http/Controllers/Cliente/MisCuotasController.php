<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;
use App\Models\Cuota;
use App\Services\FinanciamientoService;
use Illuminate\Http\Request;

/**
 * Portal del cliente. Solo ve lo suyo: el filtro sale de su propio
 * registro de cliente, nunca de un id que venga en la URL.
 */
class MisCuotasController extends Controller
{
    public function __invoke(Request $request, FinanciamientoService $financiamiento)
    {
        $cliente = $request->user()->cliente;

        abort_if($cliente === null, 403, 'Tu usuario todavia no esta vinculado a un cliente.');

        $cuotas = Cuota::with('financiamiento.venta.vehiculo')
            ->delCliente($cliente->id)
            ->orderBy('fecha_vencimiento')
            ->get();

        $cuotas->each(fn (Cuota $c) => $c->setAttribute('mora_hoy', $financiamiento->calcularMora($c)));

        return view('cliente.cuotas', [
            'cliente'   => $cliente,
            'cuotas'    => $cuotas,
            'pagado'    => $cuotas->sum('monto_pagado'),
            'pendiente' => $cuotas->sum(fn (Cuota $c) => $c->saldo),
        ]);
    }
}
