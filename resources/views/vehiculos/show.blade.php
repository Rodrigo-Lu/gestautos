@extends('layouts.app')
@section('titulo', $vehiculo->descripcion_corta)

@section('acciones')
    <a href="{{ route('vehiculos.edit', $vehiculo) }}" class="btn btn-secondary btn-sm">Editar</a>
    @if($vehiculo->estado === \App\Enums\EstadoVehiculo::DISPONIBLE)
        <a href="{{ route('ventas.create', ['vehiculo_id' => $vehiculo->id]) }}" class="btn btn-primary btn-sm">Vender</a>
    @endif
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    @forelse($vehiculo->fotos as $foto)
                        <div class="col-6 col-md-4 mb-3">
                            <img src="{{ $foto->url }}" class="img-fluid rounded" alt="">
                            <div class="mt-1 d-flex justify-content-between align-items-center">
                                @if($foto->es_principal)
                                    <span class="badge badge-success">Principal</span>
                                @else
                                    <form method="POST" action="{{ route('vehiculos.fotos.principal', [$vehiculo, $foto]) }}">
                                        @csrf
                                        <button class="btn btn-xs btn-outline-secondary">Principal</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('vehiculos.fotos.destroy', [$vehiculo, $foto]) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-xs btn-outline-danger">Quitar</button>
                                </form>
                            </div>
                        </div>
                    @empty
                    <div class="col-12 text-muted">Este vehículo todavía no tiene fotos cargadas.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ficha</h3>
                <span class="badge badge-{{ $vehiculo->estado->color() }} float-right">{{ $vehiculo->estado->etiqueta() }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr><th>Código</th><td>{{ $vehiculo->codigo_publicacion }}</td></tr>
                    <tr><th>Año</th><td>{{ $vehiculo->anio }}</td></tr>
                    <tr><th>Kilometraje</th><td>{{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</td></tr>
                    <tr><th>Transmisión</th><td>{{ $vehiculo->transmision->etiqueta() }}</td></tr>
                    <tr><th>Combustible</th><td>{{ $vehiculo->combustible->etiqueta() }}</td></tr>
                    <tr><th>Chasis</th><td>{{ $vehiculo->numero_chasis ?: '-' }}</td></tr>
                    <tr><th>Ingreso</th><td>{{ $vehiculo->fecha_ingreso->format('d/m/Y') }}</td></tr>
                    <tr><th>Precio</th><td class="font-weight-bold">{{ $vehiculo->precioFormateado() }}</td></tr>
                    @can('verCosto', App\Models\Vehiculo::class)
                        <tr class="table-warning">
                            <th>Compra</th>
                            <td>{{ $vehiculo->precio_compra ? $vehiculo->moneda->formatear($vehiculo->precio_compra) : 'sin cargar' }}</td>
                        </tr>
                    @endcan
                </table>
            </div>
        </div>

        @if($vehiculo->ventas->isNotEmpty())
            <div class="card">
                <div class="card-header"><h3 class="card-title">Historial de ventas</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        @foreach($vehiculo->ventas as $venta)
                            <tr>
                                <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                                <td>{{ $venta->cliente->nombre }}</td>
                                <td><a href="{{ route('ventas.show', $venta) }}">ver</a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
