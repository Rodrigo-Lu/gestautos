@extends('layouts.app')
@section('titulo', 'Cobrar cuota')

@section('contenido')
@php($venta = $cuota->financiamiento->venta)
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Detalle</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr><th>Cliente</th><td>{{ $venta->cliente->nombre }} (CI {{ $venta->cliente->cedula }})</td></tr>
                    <tr><th>Vehículo</th><td>{{ $venta->vehiculo->descripcion_corta }}</td></tr>
                    <tr><th>Cuota</th><td>{{ $cuota->numero_cuota }} de {{ $cuota->financiamiento->cantidad_cuotas }}</td></tr>
                    <tr><th>Vencimiento</th><td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td></tr>
                    <tr><th>Importe</th><td>{{ $venta->moneda->formatear($cuota->monto_cuota) }}</td></tr>
                    <tr><th>Ya pagado</th><td>{{ $venta->moneda->formatear($cuota->monto_pagado) }}</td></tr>
                    <tr><th>Saldo</th><td class="font-weight-bold">{{ $venta->moneda->formatear($cuota->saldo) }}</td></tr>
                    @if($cuota->dias_atraso > 0)
                        <tr class="table-danger">
                            <th>Atraso</th>
                            <td>{{ $cuota->dias_atraso }} día(s) &mdash; mora {{ $venta->moneda->formatear($mora) }}</td>
                        </tr>
                    @endif
                    <tr class="table-active">
                        <th>Total a cobrar</th>
                        <td class="font-weight-bold">{{ $venta->moneda->formatear($cuota->saldo + $mora) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <form method="POST" action="{{ route('cuotas.pagar', $cuota) }}">
            @csrf
            <div class="card">
                <div class="card-header"><h3 class="card-title">Registrar el pago</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Monto recibido</label>
                        <input type="number" step="0.01" name="monto" class="form-control"
                               value="{{ old('monto', $cuota->saldo + $mora) }}" min="0.01" required autofocus>
                        <small class="text-muted">Incluye el saldo de la cuota y la mora calculada hasta hoy. Si paga menos, queda como pago parcial.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label>Forma de pago</label>
                        <select name="forma_pago" class="form-control">
                            @foreach($formasPago as $valor => $etiqueta)
                                <option value="{{ $valor }}">{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Cobrar y emitir recibo</button>
                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-link">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
