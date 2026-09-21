@extends('layouts.pdf')
@section('tipo-doc', 'REPORTE DE VENTAS')
@section('numero-doc', \Carbon\Carbon::parse($desde)->format('d/m/Y').' a '.\Carbon\Carbon::parse($hasta)->format('d/m/Y'))

@section('contenido')
<table class="sin-borde">
    <tr><td>Unidades vendidas: <strong>{{ $cantidad }}</strong></td>
        <td class="derecha">Facturado (equiv. Gs.): <strong>{{ number_format($total_gs, 0, ',', '.') }}</strong></td></tr>
</table>

<table>
    <thead><tr><th>Fecha</th><th>Cliente</th><th>Vehículo</th><th>Vendedor</th><th>Tipo</th><th class="derecha">Importe</th></tr></thead>
    <tbody>
    @foreach($ventas as $v)
        <tr>
            <td>{{ $v->fecha->format('d/m/Y') }}</td>
            <td>{{ $v->cliente->nombre }}</td>
            <td>{{ $v->vehiculo->descripcion_corta }}</td>
            <td>{{ $v->vendedor->nombre }}</td>
            <td>{{ $v->tipo_venta->etiqueta() }}</td>
            <td class="derecha">{{ $v->moneda->formatear($v->precio_venta) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
