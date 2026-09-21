@extends('layouts.app')
@section('titulo', 'Reporte de cobranzas')

@section('acciones')
    <a href="{{ route('reportes.generar', ['tipo' => 'cobranzas', 'desde' => $desde, 'hasta' => $hasta, 'pdf' => 1]) }}"
       class="btn btn-sm btn-outline-secondary">Descargar PDF</a>
@endsection

@section('contenido')
<div class="row">
    <div class="col-md-3"><div class="info-box"><div class="info-box-content">
        <span class="info-box-text">Cobrado</span>
        <span class="info-box-number">{{ number_format($total_cobrado, 0, ',', '.') }}</span>
    </div></div></div>
    <div class="col-md-3"><div class="info-box"><div class="info-box-content">
        <span class="info-box-text">Por cobrar</span>
        <span class="info-box-number">{{ number_format($total_pendiente, 0, ',', '.') }}</span>
    </div></div></div>
    <div class="col-md-3"><div class="info-box bg-danger"><div class="info-box-content">
        <span class="info-box-text">Cuotas vencidas</span>
        <span class="info-box-number">{{ $vencidas }}</span>
    </div></div></div>
    <div class="col-md-3"><div class="info-box"><div class="info-box-content">
        <span class="info-box-text">Mora acumulada</span>
        <span class="info-box-number">{{ number_format($total_mora, 0, ',', '.') }}</span>
    </div></div></div>
</div>

<div class="card">
    <div class="card-body table-responsive p-0" style="max-height:600px">
        <table class="table table-sm table-head-fixed mb-0">
            <thead><tr><th>Vence</th><th>Cliente</th><th>Cuota</th><th class="text-right">Importe</th>
                       <th class="text-right">Pagado</th><th class="text-right">Saldo</th><th>Estado</th></tr></thead>
            <tbody>
            @foreach($cuotas as $c)
                <tr>
                    <td>{{ $c->fecha_vencimiento->format('d/m/Y') }}</td>
                    <td>{{ $c->financiamiento->venta->cliente->nombre }}</td>
                    <td>{{ $c->numero_cuota }}</td>
                    <td class="text-right">{{ number_format($c->monto_cuota, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($c->monto_pagado, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($c->saldo, 0, ',', '.') }}</td>
                    <td><span class="badge badge-{{ $c->estado->color() }}">{{ $c->estado->etiqueta() }}</span></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
