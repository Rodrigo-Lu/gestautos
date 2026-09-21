@extends('layouts.app')
@section('titulo', 'Permutas ofrecidas')

@section('contenido')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <select name="estado" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Todas</option>
                @foreach($estados as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected($estado === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0">
            <thead><tr><th>Fecha</th><th>Vehículo ofrecido</th><th>Contacto</th><th>Estado</th><th>Evaluación</th></tr></thead>
            <tbody>
            @forelse($tasaciones as $t)
                <tr>
                    <td style="width:100px">{{ $t->fecha->format('d/m/Y') }}</td>
                    <td>
                        {{ $t->descripcion }}
                        <div class="small text-muted">{{ number_format($t->kilometraje, 0, ',', '.') }} km</div>
                    </td>
                    <td>
                        {{ $t->nombre_contacto }}
                        <div class="small text-muted">{{ $t->telefono_contacto }}</div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $t->estado->color() }}">{{ $t->estado->etiqueta() }}</span>
                        @if($t->monto_ofrecido)
                            <div class="small">{{ $t->moneda->formatear($t->monto_ofrecido) }}</div>
                        @endif
                    </td>
                    <td style="width:380px">
                        <form method="POST" action="{{ route('tasaciones.evaluar', $t) }}">
                            @csrf @method('PATCH')
                            <div class="form-row">
                                <div class="col-4">
                                    <select name="estado" class="form-control form-control-sm">
                                        @foreach($estados as $valor => $etiqueta)
                                            <option value="{{ $valor }}" @selected($t->estado->value === $valor)>{{ $etiqueta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3">
                                    <select name="moneda" class="form-control form-control-sm">
                                        <option value="GS" @selected($t->moneda->value === 'GS')>Gs.</option>
                                        <option value="USD" @selected($t->moneda->value === 'USD')>US$</option>
                                    </select>
                                </div>
                                <div class="col-3">
                                    <input type="number" step="0.01" name="monto_ofrecido" class="form-control form-control-sm"
                                           value="{{ $t->monto_ofrecido }}" placeholder="Monto">
                                </div>
                                <div class="col-2">
                                    <button class="btn btn-sm btn-primary btn-block">OK</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Nadie ofreció vehículos en permuta todavía.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $tasaciones->links() }}</div>
</div>
@endsection
