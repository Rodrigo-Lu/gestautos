@extends('layouts.app')
@section('titulo', 'Reporte de rentabilidad')

@section('acciones')
    <a href="{{ route('reportes.generar', ['tipo' => 'rentabilidad', 'desde' => $desde, 'hasta' => $hasta, 'pdf' => 1]) }}"
       class="btn btn-sm btn-outline-secondary">Descargar PDF</a>
@endsection

@section('contenido')
<div class="alert alert-warning">
    Informacion reservada a la direccion: incluye precios de compra.
</div>

<div class="row">
    <div class="col-md-4"><div class="info-box"><div class="info-box-content">
        <span class="info-box-text">Margen total</span>
        <span class="info-box-number">{{ number_format($margen_total, 0, ',', '.') }}</span>
    </div></div></div>
    <div class="col-md-4"><div class="info-box"><div class="info-box-content">
        <span class="info-box-text">Ganancia por intereses</span>
        <span class="info-box-number">{{ number_format($interes_total, 0, ',', '.') }}</span>
    </div></div></div>
    <div class="col-md-4"><div class="info-box"><div class="info-box-content">
        <span class="info-box-text">Ventas sin costo cargado</span>
        <span class="info-box-number">{{ $sin_costo }}</span>
    </div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0" style="max-height:600px">
        <table class="table table-sm table-head-fixed mb-0">
            <thead><tr><th>Fecha</th><th>Vehículo</th><th class="text-right">Compra</th><th class="text-right">Venta</th>
                       <th class="text-right">Intereses</th><th class="text-right">Margen</th><th class="text-right">%</th></tr></thead>
            <tbody>
            @foreach($filas as $f)
                <tr>
                    <td>{{ $f['venta']->fecha->format('d/m/Y') }}</td>
                    <td>{{ $f['venta']->vehiculo->descripcion_corta }}</td>
                    <td class="text-right">{{ number_format($f['precio_compra'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($f['precio_venta'], 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($f['interes'], 0, ',', '.') }}</td>
                    <td class="text-right {{ $f['margen'] < 0 ? 'text-danger' : 'text-success' }}">
                        {{ number_format($f['margen'], 0, ',', '.') }}
                    </td>
                    <td class="text-right">{{ $f['margen_pct'] !== null ? $f['margen_pct'].'%' : '-' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
