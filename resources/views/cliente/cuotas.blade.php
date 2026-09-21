@extends('layouts.publico')
@section('titulo', 'Mis cuotas')

@section('contenido')
<h1 class="h4 mb-1">Hola, {{ $cliente->nombre }}</h1>
<p class="text-muted">Este es el estado de tus pagos.</p>

<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card"><div class="card-body">
            <p class="text-muted small mb-1">Pagado hasta hoy</p>
            <p class="h4 mb-0 text-success">{{ number_format($pagado, 0, ',', '.') }}</p>
        </div></div>
    </div>
    <div class="col-md-6 mb-3">
        <div class="card"><div class="card-body">
            <p class="text-muted small mb-1">Saldo pendiente</p>
            <p class="h4 mb-0">{{ number_format($pendiente, 0, ',', '.') }}</p>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr><th>Cuota</th><th>Vehículo</th><th>Vence</th>
                    <th class="text-end">Importe</th><th class="text-end">Pagado</th>
                    <th class="text-end">Mora</th><th>Estado</th></tr>
            </thead>
            <tbody>
            @forelse($cuotas as $c)
                @php($venta = $c->financiamiento->venta)
                <tr class="{{ $c->fecha_vencimiento->isPast() && ! $c->estaPagada() ? 'table-danger' : '' }}">
                    <td>{{ $c->numero_cuota }}/{{ $c->financiamiento->cantidad_cuotas }}</td>
                    <td class="small">{{ $venta->vehiculo->descripcion_corta }}</td>
                    <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
                    <td class="text-end">{{ $venta->moneda->formatear($c->monto_cuota) }}</td>
                    <td class="text-end">{{ $venta->moneda->formatear($c->monto_pagado) }}</td>
                    <td class="text-end">{{ $c->mora_hoy > 0 ? $venta->moneda->formatear($c->mora_hoy) : '-' }}</td>
                    <td><span class="badge bg-{{ $c->estado->color() }}">{{ $c->estado->etiqueta() }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No tenes cuotas registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<p class="small text-muted mt-3">
    Para pagar o consultar acercate a {{ config('gestautos.negocio.direccion') }}
    o llamanos al {{ config('gestautos.negocio.telefono') }}.
</p>
@endsection
