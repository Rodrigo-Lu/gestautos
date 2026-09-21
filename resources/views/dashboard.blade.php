@extends('layouts.app')
@section('titulo', 'Inicio')

@section('acciones')
    <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">Registrar venta</a>
@endsection

@section('contenido')
<div class="row">
    <div class="col-6 col-lg-3">
        <div class="small-box bg-success">
            <div class="inner"><h3>{{ $disponibles }}</h3><p>Vehículos disponibles</p></div>
            <a href="{{ route('vehiculos.index', ['estado' => 'DISPONIBLE']) }}" class="small-box-footer">Ver stock</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $vendidosMes }}</h3>
                <p>Ventas de {{ now()->translatedFormat('F') }}</p>
            </div>
            <a href="{{ route('ventas.index') }}" class="small-box-footer">Ver ventas</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="small-box bg-danger">
            <div class="inner"><h3>{{ $cuotasVencidas }}</h3><p>Cuotas vencidas</p></div>
            <a href="{{ route('cuotas.index', ['vista' => 'vencidas']) }}" class="small-box-footer">Ir a cobranzas</a>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="small-box bg-warning">
            <div class="inner"><h3>{{ $cuotasPorVencer }}</h3><p>Cuotas por vencer</p></div>
            <a href="{{ route('cuotas.index', ['vista' => 'por_vencer']) }}" class="small-box-footer">Ver proximas</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Últimas ventas</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Fecha</th><th>Cliente</th><th>Vehículo</th><th class="text-right">Importe</th></tr></thead>
                    <tbody>
                    @forelse($ultimasVentas as $v)
                        <tr>
                            <td>{{ $v->fecha->format('d/m/Y') }}</td>
                            <td><a href="{{ route('clientes.show', $v->cliente) }}">{{ $v->cliente->nombre }}</a></td>
                            <td>{{ $v->vehiculo->descripcion_corta }}</td>
                            <td class="text-right">{{ $v->moneda->formatear($v->precio_venta) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Todavia no hay ventas cargadas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Próximos vencimientos</h3>
                <small class="text-muted float-right">Rojo = cuota atrasada</small>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Vence</th><th>Cliente / vehículo</th><th>Cuota</th><th>Mora</th><th></th></tr></thead>
                    <tbody>
                    @forelse($proximasCuotas as $c)
                        @php($venta = $c->financiamiento->venta)
                        <tr class="{{ $c->dias_atraso > 0 ? 'table-danger' : '' }}">
                            <td>{{ $c->fecha_vencimiento->format('d/m') }}</td>
                            <td>
                                <div>{{ $venta->cliente->nombre }}</div>
                                <small class="text-muted">{{ $venta->vehiculo->descripcion_corta }}</small>
                                @if($c->dias_atraso > 0)
                                    <div class="small text-danger">{{ $c->dias_atraso }} días de atraso</div>
                                @endif
                            </td>
                            <td>{{ $c->numero_cuota }}/{{ $c->financiamiento->cantidad_cuotas }}</td>
                            <td>{{ $c->mora_hoy > 0 ? $venta->moneda->formatear($c->mora_hoy) : '-' }}</td>
                            <td class="text-right">
                                <a href="{{ route('cuotas.cobrar', $c) }}" class="btn btn-xs btn-outline-primary">Cobrar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No hay cuotas pendientes.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Pendientes de atención</h3></div>
            <div class="card-body">
                <p class="mb-2">Consultas web sin responder: <strong>{{ $consultasNuevas }}</strong>
                    <a href="{{ route('consultas.index') }}" class="small ml-2">abrir</a></p>
                <p class="mb-0">Permutas sin evaluar: <strong>{{ $tasacionesNuevas }}</strong>
                    <a href="{{ route('tasaciones.index') }}" class="small ml-2">abrir</a></p>
            </div>
        </div>
    </div>
</div>
@endsection
