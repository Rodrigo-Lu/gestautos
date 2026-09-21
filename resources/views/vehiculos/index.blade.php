@extends('layouts.app')
@section('titulo', 'Vehículos')

@section('acciones')
    <a href="{{ route('vehiculos.create') }}" class="btn btn-primary btn-sm">Cargar vehículo</a>
@endsection

@section('contenido')
<div class="card card-outline card-primary">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-md-4 mb-2">
                <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" class="form-control"
                       placeholder="Marca, modelo, codigo o chasis">
            </div>
            <div class="col-md-2 mb-2">
                <select name="marca" class="form-control">
                    <option value="">Todas las marcas</option>
                    @foreach($marcas as $m)
                        <option value="{{ $m }}" @selected(($filtros['marca'] ?? '') === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <select name="estado" class="form-control">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $valor => $etiqueta)
                        <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $etiqueta }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <input type="number" name="precio_desde" value="{{ $filtros['precio_desde'] ?? '' }}"
                       class="form-control" placeholder="Precio desde">
            </div>
            <div class="col-md-2 mb-2">
                <button class="btn btn-primary w-100">Buscar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Código</th><th>Vehículo</th><th>Año</th><th>Km</th>
                    <th class="text-right">Precio</th><th>Estado</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($vehiculos as $v)
                <tr>
                    <td class="text-muted">{{ $v->codigo_publicacion }}</td>
                    <td>
                        <a href="{{ route('vehiculos.show', $v) }}">{{ $v->marca }} {{ $v->modelo }}</a>
                        <div class="small text-muted">{{ $v->version }} {{ $v->color }}</div>
                    </td>
                    <td>{{ $v->anio }}</td>
                    <td>{{ number_format($v->kilometraje, 0, ',', '.') }}</td>
                    <td class="text-right">{{ $v->precioFormateado() }}</td>
                    <td><span class="badge badge-{{ $v->estado->color() }}">{{ $v->estado->etiqueta() }}</span></td>
                    <td class="text-right">
                        <a href="{{ route('vehiculos.edit', $v) }}" class="btn btn-xs btn-outline-secondary">Editar</a>
                        @if($v->estado === \App\Enums\EstadoVehiculo::DISPONIBLE)
                            <a href="{{ route('ventas.create', ['vehiculo_id' => $v->id]) }}"
                               class="btn btn-xs btn-primary">Vender</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">
                    No hay vehículos que coincidan con la búsqueda.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $vehiculos->links() }}</div>
</div>
@endsection
