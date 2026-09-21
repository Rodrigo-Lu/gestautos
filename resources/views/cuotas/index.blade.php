@extends('layouts.app')
@section('titulo', 'Cobranzas')

@section('contenido')
<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="btn-group mb-3">
            <a href="{{ route('cuotas.index') }}"
               class="btn btn-sm btn-{{ blank($filtros['vista'] ?? null) ? 'primary' : 'outline-primary' }}">Todas</a>
            <a href="{{ route('cuotas.index', ['vista' => 'vencidas']) }}"
               class="btn btn-sm btn-{{ ($filtros['vista'] ?? '') === 'vencidas' ? 'danger' : 'outline-danger' }}">Vencidas</a>
            <a href="{{ route('cuotas.index', ['vista' => 'por_vencer']) }}"
               class="btn btn-sm btn-{{ ($filtros['vista'] ?? '') === 'por_vencer' ? 'warning' : 'outline-warning' }}">Por vencer</a>
        </div>

        <form method="GET" class="form-inline filtro-responsive">
            <input type="hidden" name="vista" value="{{ $filtros['vista'] ?? '' }}">
            <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}" class="form-control mr-2"
                   placeholder="Nombre o cédula del cliente" style="min-width:260px">
            <select name="estado" class="form-control mr-2">
                <option value="">Todos los estados</option>
                @foreach($estados as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected(($filtros['estado'] ?? '') === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
            <button class="btn btn-primary">Buscar</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>Vence</th><th>Cliente</th><th>Vehículo</th><th>Cuota</th>
                    <th class="text-right">Importe</th><th class="text-right">Saldo</th>
                    <th class="text-right">Mora hoy</th><th>Estado</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($cuotas as $c)
                @php($venta = $c->financiamiento->venta)
                <tr class="{{ $c->fecha_vencimiento->isPast() && ! $c->estaPagada() ? 'table-danger' : '' }}">
                    <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
                    <td><a href="{{ route('clientes.show', $venta->cliente) }}">{{ $venta->cliente->nombre }}</a></td>
                    <td class="small">{{ $venta->vehiculo->descripcion_corta }}</td>
                    <td>{{ $c->numero_cuota }}/{{ $c->financiamiento->cantidad_cuotas }}</td>
                    <td class="text-right">{{ $venta->moneda->formatear($c->monto_cuota) }}</td>
                    <td class="text-right">{{ $venta->moneda->formatear($c->saldo) }}</td>
                    <td class="text-right {{ $c->mora_hoy > 0 ? 'text-danger' : 'text-muted' }}">
                        {{ $venta->moneda->formatear($c->mora_hoy) }}
                    </td>
                    <td><span class="badge badge-{{ $c->estado->color() }}">{{ $c->estado->etiqueta() }}</span></td>
                    <td class="text-right">
                        @if(! $c->estaPagada())
                            <a href="{{ route('cuotas.cobrar', $c) }}" class="btn btn-xs btn-primary">Cobrar</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No hay cuotas que coincidan con el filtro.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $cuotas->links() }}</div>
</div>
@endsection
