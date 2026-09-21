@extends('layouts.app')
@section('titulo', $cliente->nombre)

@section('acciones')
    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-secondary btn-sm">Editar</a>
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Datos</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr><th>Cédula</th><td>{{ $cliente->cedula }}</td></tr>
                    <tr><th>Teléfono</th><td>{{ $cliente->telefono }}</td></tr>
                    <tr><th>Correo</th><td>{{ $cliente->email ?: '-' }}</td></tr>
                    <tr><th>Dirección</th><td>{{ $cliente->direccion ?: '-' }}</td></tr>
                </table>
            </div>
            @can('gestionarAcceso', $cliente)
                <div class="card-footer">
                    @if($cliente->tieneAcceso())
                        <p class="small mb-2">Accede al portal con {{ $cliente->usuario->email }}</p>
                        <form method="POST" action="{{ route('clientes.acceso.revocar', $cliente) }}"
                              onsubmit="return confirm('Se elimina el acceso del cliente al portal. Continuar?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Revocar acceso</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('clientes.acceso.crear', $cliente) }}">
                            @csrf
                            <button class="btn btn-sm btn-outline-primary">Crear acceso al portal</button>
                        </form>
                    @endif
                </div>
            @endcan
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Compras</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Fecha</th><th>Vehículo</th><th>Tipo</th><th class="text-right">Importe</th><th>Cuotas</th><th></th></tr></thead>
                    <tbody>
                    @forelse($cliente->ventas as $venta)
                        <tr>
                            <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td>{{ $venta->vehiculo->descripcion_corta }}</td>
                            <td>{{ $venta->tipo_venta->etiqueta() }}</td>
                            <td class="text-right">{{ $venta->moneda->formatear($venta->precio_venta) }}</td>
                            <td>
                                @if($venta->financiamiento)
                                    {{ $venta->financiamiento->cuotas_pendientes }} pendientes
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="text-right"><a href="{{ route('ventas.show', $venta) }}">ver</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Este cliente todavía no compró.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($cliente->consultas->isNotEmpty())
            <div class="card">
                <div class="card-header"><h3 class="card-title">Consultas web</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        @foreach($cliente->consultas as $consulta)
                            <tr>
                                <td style="width:100px">{{ $consulta->fecha->format('d/m/Y') }}</td>
                                <td>{{ $consulta->vehiculo?->descripcion_corta ?? 'consulta general' }}</td>
                                <td><span class="badge badge-{{ $consulta->estado->color() }}">{{ $consulta->estado->etiqueta() }}</span></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
