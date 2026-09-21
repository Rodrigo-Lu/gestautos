@extends('layouts.publico')
@section('titulo', 'Simulador de cuotas')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-8">
        @if($vehiculo)
            <p class="text-muted">
                <a href="{{ route('catalogo.show', $vehiculo) }}">{{ $vehiculo->descripcion_corta }}</a>
            </p>
        @endif

        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <p class="text-muted mb-1">{{ $cuotas }} cuotas de</p>
                <p class="display-5 mb-3">{{ $moneda->formatear($resultado['valor_cuota']) }}</p>
                <div class="row small text-muted">
                    <div class="col-4">Precio<br><strong class="text-dark">{{ $moneda->formatear($precio) }}</strong></div>
                    <div class="col-4">Entrega<br><strong class="text-dark">{{ $moneda->formatear($anticipo) }}</strong></div>
                    <div class="col-4">Total financiado<br><strong class="text-dark">{{ $moneda->formatear($resultado['total']) }}</strong></div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-white"><h2 class="h6 mb-0">Otros plazos con la misma entrega</h2></div>
            <table class="table table-sm mb-0">
                <thead><tr><th>Cuotas</th><th class="text-end">Valor de cuota</th><th class="text-end">Total a pagar</th></tr></thead>
                <tbody>
                @foreach($comparativo as $fila)
                    <tr class="{{ $fila['cantidad_cuotas'] === $cuotas ? 'table-active' : '' }}">
                        <td>{{ $fila['cantidad_cuotas'] }}</td>
                        <td class="text-end">{{ $moneda->formatear($fila['valor_cuota']) }}</td>
                        <td class="text-end">{{ $moneda->formatear($fila['total']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <p class="small text-muted">
            Calculo referencial con tasa del {{ $tasa }}% mensual sobre saldo, sistema de cuota fija.
            El plan definitivo se confirma en el local y queda sujeto a aprobacion crediticia.
        </p>

        @if($vehiculo)
            <a href="{{ route('catalogo.show', $vehiculo) }}" class="btn btn-dark">Consultar por este vehículo</a>
        @else
            <a href="{{ route('catalogo.index') }}" class="btn btn-dark">Ver vehiculos disponibles</a>
        @endif
    </div>
</div>
@endsection
