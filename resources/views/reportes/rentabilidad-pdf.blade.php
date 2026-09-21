@extends('layouts.pdf')
@section('tipo-doc', 'REPORTE DE RENTABILIDAD')
@section('numero-doc', \Carbon\Carbon::parse($desde)->format('d/m/Y').' a '.\Carbon\Carbon::parse($hasta)->format('d/m/Y'))

@section('contenido')
<p><strong>Documento reservado a la direccion.</strong></p>
<table class="sin-borde">
    <tr>
        <td>Margen total: <strong>{{ number_format($margen_total, 0, ',', '.') }}</strong></td>
        <td class="derecha">Ganancia por intereses: <strong>{{ number_format($interes_total, 0, ',', '.') }}</strong></td>
    </tr>
</table>

<table>
    <thead><tr><th>Fecha</th><th>Vehículo</th><th class="derecha">Compra</th><th class="derecha">Venta</th>
               <th class="derecha">Intereses</th><th class="derecha">Margen</th><th class="derecha">%</th></tr></thead>
    <tbody>
    @foreach($filas as $f)
        <tr>
            <td>{{ $f['venta']->fecha->format('d/m/Y') }}</td>
            <td>{{ $f['venta']->vehiculo->descripcion_corta }}</td>
            <td class="derecha">{{ number_format($f['precio_compra'], 0, ',', '.') }}</td>
            <td class="derecha">{{ number_format($f['precio_venta'], 0, ',', '.') }}</td>
            <td class="derecha">{{ number_format($f['interes'], 0, ',', '.') }}</td>
            <td class="derecha">{{ number_format($f['margen'], 0, ',', '.') }}</td>
            <td class="derecha">{{ $f['margen_pct'] !== null ? $f['margen_pct'].'%' : '-' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
