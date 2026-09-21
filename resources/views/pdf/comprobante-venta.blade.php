@extends('layouts.pdf')
@section('tipo-doc', 'COMPROBANTE DE VENTA')
@section('numero-doc', $comprobante->numero)

@section('contenido')
<table class="sin-borde">
    <tr>
        <td width="50%">
            <strong>Comprador</strong><br>
            {{ $venta->cliente->nombre }}<br>
            CI {{ $venta->cliente->cedula }}<br>
            {{ $venta->cliente->telefono }}<br>
            {{ $venta->cliente->direccion }}
        </td>
        <td width="50%">
            <strong>Operación</strong><br>
            Fecha: {{ $venta->fecha->format('d/m/Y') }}<br>
            Tipo: {{ $venta->tipo_venta->etiqueta() }}<br>
            Vendedor: {{ $venta->vendedor->nombre }}
        </td>
    </tr>
</table>

<h1>Vehículo</h1>
<table>
    <tr><th>Código</th><td>{{ $venta->vehiculo->codigo_publicacion }}</td>
        <th>Chasis</th><td>{{ $venta->vehiculo->numero_chasis ?: '-' }}</td></tr>
    <tr><th>Marca y modelo</th><td>{{ $venta->vehiculo->marca }} {{ $venta->vehiculo->modelo }} {{ $venta->vehiculo->version }}</td>
        <th>Año</th><td>{{ $venta->vehiculo->anio }}</td></tr>
    <tr><th>Kilometraje</th><td>{{ number_format($venta->vehiculo->kilometraje, 0, ',', '.') }} km</td>
        <th>Color</th><td>{{ $venta->vehiculo->color }}</td></tr>
</table>

<h1>Condiciones</h1>
<table>
    <tr><th>Precio de venta</th><td class="derecha">{{ $venta->moneda->formatear($venta->precio_venta) }}</td></tr>
    @if($venta->monto_permuta > 0)
        <tr><th>Permuta recibida</th><td class="derecha">- {{ $venta->moneda->formatear($venta->monto_permuta) }}</td></tr>
    @endif
    @if($venta->financiamiento)
        <tr><th>Anticipo</th><td class="derecha">{{ $venta->moneda->formatear($venta->financiamiento->monto_anticipo) }}</td></tr>
        <tr><th>Monto financiado</th><td class="derecha">{{ $venta->moneda->formatear($venta->financiamiento->monto_financiado) }}</td></tr>
        <tr><th>Interés mensual</th><td class="derecha">{{ number_format($venta->financiamiento->tasa_interes_mensual, 2, ',', '.') }}%</td></tr>
        <tr class="total"><th>Total a pagar en cuotas</th>
            <td class="derecha">{{ $venta->moneda->formatear($venta->financiamiento->monto_total_con_interes) }}</td></tr>
    @else
        <tr class="total"><th>Total abonado al contado</th>
            <td class="derecha">{{ $venta->moneda->formatear($venta->monto_neto) }}</td></tr>
    @endif
</table>

@if($venta->financiamiento)
    <h1>Plan de pagos</h1>
    <table>
        <thead><tr><th>Cuota</th><th>Vencimiento</th><th class="derecha">Capital</th>
                   <th class="derecha">Interés</th><th class="derecha">Importe</th></tr></thead>
        <tbody>
        @foreach($venta->financiamiento->cuotas as $c)
            <tr>
                <td>{{ $c->numero_cuota }}</td>
                <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
                <td class="derecha">{{ $venta->moneda->formatear($c->monto_capital) }}</td>
                <td class="derecha">{{ $venta->moneda->formatear($c->monto_interes) }}</td>
                <td class="derecha">{{ $venta->moneda->formatear($c->monto_cuota) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <p>Mora por atraso: {{ number_format($venta->financiamiento->tasa_mora_diaria, 2, ',', '.') }}% diario sobre el saldo de la cuota.</p>
@endif

<table class="firma">
    <tr>
        <td>{{ $venta->cliente->nombre }}<br>Comprador</td>
        <td></td>
        <td>{{ config('gestautos.negocio.nombre') }}<br>Vendedor</td>
    </tr>
</table>
@endsection
