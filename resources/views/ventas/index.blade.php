@extends('layouts.app')
@section('titulo', 'Ventas')

@section('acciones')
    <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">Registrar venta</a>
@endsection

@section('contenido')
<div class="card card-outline card-primary">
    <div class="card-body">
        <form method="GET" class="form-row">
            <div class="col-md-3 mb-2">
                <select name="estado" class="form-control">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <select name="tipo" class="form-control">
                    <option value="">Contado y financiado</option>
                    @foreach($tipos as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(($filtros['tipo'] ?? '') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" name="desde" value="{{ $filtros['desde'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2 mb-2">
                <input type="date" name="hasta" value="{{ $filtros['hasta'] ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2 mb-2"><button class="btn btn-primary w-100">Filtrar</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Fecha</th><th>Cliente</th><th>Vehículo</th><th>Vendedor</th>
                    <th>Tipo</th><th class="text-right">Importe</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($ventas as $v)
                <tr>
                    <td>{{ $v->fecha->format('d/m/Y') }}</td>
                    <td>{{ $v->cliente->nombre }}</td>
                    <td>{{ $v->vehiculo->descripcion_corta }}</td>
                    <td class="small text-muted">{{ $v->vendedor->nombre }}</td>
                    <td>
                        {{ $v->tipo_venta->etiqueta() }}
                        @if($v->financiamiento)
                            <span class="small text-muted">({{ $v->financiamiento->cantidad_cuotas }} cuotas)</span>
                        @endif
                    </td>
                    <td class="text-right">{{ $v->moneda->formatear($v->precio_venta) }}</td>
                    <td><span class="badge badge-{{ $v->estado->color() }}">{{ $v->estado->etiqueta() }}</span></td>
                    <td class="text-right"><a href="{{ route('ventas.show', $v) }}">ver</a></td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No hay ventas en ese periodo.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $ventas->links() }}</div>
</div>
@endsection
