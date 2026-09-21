@extends('layouts.app')
@section('titulo', 'Reporte de inventario')

@section('acciones')
    <a href="{{ route('reportes.generar', ['tipo' => 'inventario', 'pdf' => 1]) }}"
       class="btn btn-sm btn-outline-secondary">Descargar PDF</a>
@endsection

@section('contenido')
<div class="row">
    <div class="col-md-6">
        <div class="info-box"><div class="info-box-content">
            <span class="info-box-text">Disponibles ahora</span>
            <span class="info-box-number">{{ $disponibles }}</span>
        </div></div>
    </div>
    <div class="col-md-6">
        <div class="info-box"><div class="info-box-content">
            <span class="info-box-text">Antigüedad promedio en stock</span>
            <span class="info-box-number">{{ round($antiguedad ?? 0) }} días</span>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Stock completo</h3></div>
    <div class="card-body table-responsive p-0" style="max-height:600px">
        <table class="table table-sm table-head-fixed mb-0">
            <thead><tr><th>Código</th><th>Vehículo</th><th>Año</th><th>Km</th><th>Ingreso</th><th>Días</th><th class="text-right">Precio</th><th>Estado</th></tr></thead>
            <tbody>
            @foreach($vehiculos as $v)
                <tr>
                    <td>{{ $v->codigo_publicacion }}</td>
                    <td>{{ $v->descripcion_corta }}</td>
                    <td>{{ $v->anio }}</td>
                    <td>{{ number_format($v->kilometraje, 0, ',', '.') }}</td>
                    <td>{{ $v->fecha_ingreso->format('d/m/Y') }}</td>
                    <td>{{ (int) $v->fecha_ingreso->diffInDays(now()) }}</td>
                    <td class="text-right">{{ $v->precioFormateado() }}</td>
                    <td><span class="badge badge-{{ $v->estado->color() }}">{{ $v->estado->etiqueta() }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
