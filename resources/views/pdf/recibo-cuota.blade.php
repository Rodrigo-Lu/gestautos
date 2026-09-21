@extends('layouts.pdf')
@section('tipo-doc', 'RECIBO DE PAGO')
@section('numero-doc', $comprobante->numero)

@section('contenido')
<table class="sin-borde">
    <tr>
        <td width="60%">
            <strong>Recibimos de</strong><br>
            {{ $venta->cliente->nombre }} &bull; CI {{ $venta->cliente->cedula }}
        </td>
        <td width="40%">
            <strong>Fecha</strong><br>{{ $comprobante->fecha_emision->format('d/m/Y') }}
        </td>
    </tr>
</table>

<table>
    <tr><th>Concepto</th>
        <td>Cuota {{ $cuota->numero_cuota }} de {{ $cuota->financiamiento->cantidad_cuotas }} &mdash;
            {{ $venta->vehiculo->descripcion_corta }}</td></tr>
    <tr><th>Vencimiento</th><td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td></tr>
    <tr><th>Forma de pago</th><td>{{ $cuota->forma_pago?->etiqueta() }}</td></tr>
    <tr><th>Importe de la cuota</th><td class="derecha">{{ $venta->moneda->formatear($cuota->monto_cuota) }}</td></tr>
    @if($cuota->monto_mora > 0)
        <tr><th>Mora por atraso</th><td class="derecha">{{ $venta->moneda->formatear($cuota->monto_mora) }}</td></tr>
    @endif
    <tr class="total"><th>Monto recibido</th><td class="derecha">{{ $venta->moneda->formatear($monto) }}</td></tr>
    <tr><th>Saldo de esta cuota</th><td class="derecha">{{ $venta->moneda->formatear($cuota->saldo) }}</td></tr>
    <tr><th>Saldo total del plan</th>
        <td class="derecha">{{ $venta->moneda->formatear($cuota->financiamiento->saldo) }}</td></tr>
</table>

<table class="firma">
    <tr>
        <td>{{ $comprobante->emisor->nombre }}<br>Recibi conforme</td>
        <td></td>
        <td>{{ $venta->cliente->nombre }}<br>Cliente</td>
    </tr>
</table>
@endsection
