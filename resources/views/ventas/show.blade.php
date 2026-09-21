@extends('layouts.app')
@section('titulo', 'Venta #'.$venta->id)

@section('acciones')
    @foreach($venta->comprobantes as $comp)
        <a href="{{ route('comprobantes.descargar', $comp) }}" class="btn btn-sm btn-outline-secondary">
            {{ $comp->numero }}
        </a>
    @endforeach
@endsection

@section('contenido')
<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Resumen</h3>
                <span class="badge badge-{{ $venta->estado->color() }} float-right">{{ $venta->estado->etiqueta() }}</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr><th>Fecha</th><td>{{ $venta->fecha->format('d/m/Y') }}</td></tr>
                    <tr><th>Cliente</th><td><a href="{{ route('clientes.show', $venta->cliente) }}">{{ $venta->cliente->nombre }}</a></td></tr>
                    <tr><th>Vehículo</th><td><a href="{{ route('vehiculos.show', $venta->vehiculo) }}">{{ $venta->vehiculo->descripcion_corta }}</a></td></tr>
                    <tr><th>Vendedor</th><td>{{ $venta->vendedor->nombre }}</td></tr>
                    <tr><th>Tipo</th><td>{{ $venta->tipo_venta->etiqueta() }}</td></tr>
                    <tr><th>Precio</th><td class="font-weight-bold">{{ $venta->moneda->formatear($venta->precio_venta) }}</td></tr>
                    @if($venta->monto_permuta > 0)
                        <tr><th>Permuta</th><td>- {{ $venta->moneda->formatear($venta->monto_permuta) }}</td></tr>
                    @endif
                    @if($venta->moneda === \App\Enums\Moneda::USD)
                        <tr><th>Cotizacion</th><td>{{ number_format($venta->tipo_cambio, 0, ',', '.') }}</td></tr>
                    @endif
                </table>
            </div>
            @if($venta->observaciones)
                <div class="card-footer small text-muted">{{ $venta->observaciones }}</div>
            @endif
        </div>

        @can('anular', $venta)
            <div class="card card-outline card-danger">
                <div class="card-header"><h3 class="card-title">Anular venta</h3></div>
                <form method="POST" action="{{ route('ventas.anular', $venta) }}"
                      onsubmit="return confirm('El vehículo vuelve al stock disponible. ¿Confirmar la anulación?')">
                    @csrf
                    <div class="card-body">
                        <textarea name="motivo_anulacion" rows="2" class="form-control"
                                  placeholder="Motivo de la anulacion" required></textarea>
                    </div>
                    <div class="card-footer"><button class="btn btn-sm btn-danger">Anular</button></div>
                </form>
            </div>
        @endcan

        @if($venta->estado === \App\Enums\EstadoVenta::ANULADA)
            <div class="alert alert-danger">
                <strong>Venta anulada.</strong><br>{{ $venta->motivo_anulacion }}
            </div>
        @endif
    </div>

    <div class="col-lg-8">
        @if($venta->financiamiento)
            @php($f = $venta->financiamiento)
            <div class="card">
                <div class="card-header"><h3 class="card-title">Plan de pagos</h3></div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-6 col-md-3">
                            <div class="description-block">
                                <span class="description-text">ANTICIPO</span>
                                <h5 class="description-header">{{ $venta->moneda->formatear($f->monto_anticipo) }}</h5>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="description-block">
                                <span class="description-text">FINANCIADO</span>
                                <h5 class="description-header">{{ $venta->moneda->formatear($f->monto_financiado) }}</h5>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="description-block">
                                <span class="description-text">PAGADO</span>
                                <h5 class="description-header text-success">{{ $venta->moneda->formatear($f->total_pagado) }}</h5>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="description-block">
                                <span class="description-text">SALDO</span>
                                <h5 class="description-header text-danger">{{ $venta->moneda->formatear($f->saldo) }}</h5>
                            </div>
                        </div>
                    </div>

                    <p class="small text-muted">
                        {{ $f->cantidad_cuotas }} cuotas al {{ number_format($f->tasa_interes_mensual, 2, ',', '.') }}% mensual.
                        Primer vencimiento {{ $f->fecha_primer_vencimiento->format('d/m/Y') }}.
                    </p>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead>
                            <tr><th>#</th><th>Vence</th><th class="text-right">Cuota</th>
                                <th class="text-right">Capital</th><th class="text-right">Interés</th>
                                <th class="text-right">Pagado</th><th>Estado</th><th>Cobro</th><th></th></tr>
                        </thead>
                        <tbody>
                        @foreach($f->cuotas as $c)
                            <tr>
                                <td>{{ $c->numero_cuota }}</td>
                                <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
                                <td class="text-right">{{ $venta->moneda->formatear($c->monto_cuota) }}</td>
                                <td class="text-right text-muted">{{ $venta->moneda->formatear($c->monto_capital) }}</td>
                                <td class="text-right text-muted">{{ $venta->moneda->formatear($c->monto_interes) }}</td>
                                <td class="text-right">{{ $venta->moneda->formatear($c->monto_pagado) }}</td>
                                <td><span class="badge badge-{{ $c->estado->color() }}">{{ $c->estado->etiqueta() }}</span></td>
                                <td class="small text-muted">
                                    {{ $c->fecha_pago?->format('d/m/Y') }}
                                    {{ $c->cobrador?->nombre }}
                                </td>
                                <td class="text-right">
                                    @if(! $c->estaPagada())
                                        <a href="{{ route('cuotas.cobrar', $c) }}" class="btn btn-xs btn-primary">Cobrar</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="callout callout-info">
                Venta al contado: no hay cuotas asociadas.
            </div>
        @endif
    </div>
</div>
@endsection
