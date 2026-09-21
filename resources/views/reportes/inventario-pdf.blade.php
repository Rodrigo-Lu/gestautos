@extends('layouts.pdf')
@section('tipo-doc', 'REPORTE DE INVENTARIO')
@section('numero-doc', now()->format('d/m/Y'))

@section('contenido')
<p>Vehículos disponibles: <strong>{{ $disponibles }}</strong> &bull;
   Antigüedad promedio en stock: <strong>{{ round($antiguedad ?? 0) }} días</strong></p>

<table>
    <thead><tr><th>Código</th><th>Vehículo</th><th>Año</th><th>Km</th><th>Ingreso</th><th class="derecha">Precio</th><th>Estado</th></tr></thead>
    <tbody>
    @foreach($vehiculos as $v)
        <tr>
            <td>{{ $v->codigo_publicacion }}</td>
            <td>{{ $v->descripcion_corta }}</td>
            <td>{{ $v->anio }}</td>
            <td>{{ number_format($v->kilometraje, 0, ',', '.') }}</td>
            <td>{{ $v->fecha_ingreso->format('d/m/Y') }}</td>
            <td class="derecha">{{ $v->precioFormateado() }}</td>
            <td>{{ $v->estado->etiqueta() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endsection
