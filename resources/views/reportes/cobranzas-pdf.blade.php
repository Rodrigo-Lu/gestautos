@extends('layouts.pdf')
@section('tipo-doc', 'REPORTE DE COBRANZAS')
@section('numero-doc', \Carbon\Carbon::parse($desde)->format('d/m/Y').' a '.\Carbon\Carbon::parse($hasta)->format('d/m/Y'))

@section('contenido')
<table class="sin-borde">
    <tr>
        <td>Cobrado: <strong>{{ number_format($total_cobrado, 0, ',', '.') }}</strong></td>
        <td>Por cobrar: <strong>{{ number_format($total_pendiente, 0, ',', '.') }}</strong></td>
        <td>Vencidas: <strong>{{ $vencidas }}</strong></td>
        <td class="derecha">Mora: <strong>{{ number_format($total_mora, 0, ',', '.') }}</strong></td>
    </tr>
</table>

<table>
    <thead><tr><th>Vence</th><th>Cliente</th><th>Cuota</th><th class="derecha">Importe</th>
               <th class="derecha">Pagado</th><th class="derecha">Saldo</th><th>Estado</th></tr></thead>
    <tbody>
    @foreach($cuotas as $c)
        <tr>
            <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
            <td>{{ $c->financiamiento->venta->cliente->nombre }}</td>
            <td>{{ $c->numero_cuota }}</td>
            <td class="derecha">{{ number_format($c->monto_cuota, 0, ',', '.') }}</td>
            <td class="derecha">{{ number_format($c->monto_pagado, 0, ',', '.') }}</td>
            <td class="derecha">{{ number_format($c->saldo, 0, ',', '.') }}</td>
            <td>{{ $c->estado->etiqueta() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
