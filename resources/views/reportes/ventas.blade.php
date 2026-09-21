@extends('layouts.app')
@section('titulo', 'Reporte de ventas')

@section('acciones')
    <a href="{{ route('reportes.generar', ['tipo' => 'ventas', 'desde' => $desde, 'hasta' => $hasta, 'pdf' => 1]) }}"
       class="btn btn-sm btn-outline-secondary">Descargar PDF</a>
@endsection

@section('contenido')
<p class="text-muted">Periodo: {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} a {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>

<div class="row">
    <div class="col-md-4">
        <div class="info-box"><div class="info-box-content">
            <span class="info-box-text">Unidades vendidas</span>
            <span class="info-box-number">{{ $cantidad }}</span>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="info-box"><div class="info-box-content">
            <span class="info-box-text">Facturado (equivalente Gs.)</span>
            <span class="info-box-number">{{ number_format($total_gs, 0, ',', '.') }}</span>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="info-box"><div class="info-box-content">
            <span class="info-box-text">Promedio por venta</span>
            <span class="info-box-number">{{ $cantidad ? number_format($total_gs / $cantidad, 0, ',', '.') : 0 }}</span>
        </div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Por vendedor</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    @foreach($por_vendedor as $nombre => $datos)
                        <tr>
                            <td>{{ $nombre }}</td>
                            <td class="text-right">{{ $datos['cantidad'] }}</td>
                            <td class="text-right">{{ number_format($datos['total_gs'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Detalle</h3></div>
            <div class="card-body table-responsive p-0" style="max-height:480px">
                <table class="table table-sm table-head-fixed mb-0">
                    <thead><tr><th>Fecha</th><th>Cliente</th><th>Vehículo</th><th>Tipo</th><th class="text-right">Importe</th></tr></thead>
                    <tbody>
                    @foreach($ventas as $v)
                        <tr>
                            <td>{{ $v->fecha->format('d/m/Y') }}</td>
                            <td>{{ $v->cliente->nombre }}</td>
                            <td>{{ $v->vehiculo->descripcion_corta }}</td>
                            <td>{{ $v->tipo_venta->etiqueta() }}</td>
                            <td class="text-right">{{ $v->moneda->formatear($v->precio_venta) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
