@extends('layouts.app')
@section('titulo', 'Consultas web')

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
        <table class="table table-hover mb-0">
            <thead><tr><th>Fecha</th><th>Contacto</th><th>Vehículo</th><th>Mensaje</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @forelse($consultas as $c)
                <tr>
                    <td style="width:100px">{{ $c->fecha->format('d/m/Y') }}</td>
                    <td>
                        {{ $c->nombre }}
                        <div class="small text-muted">{{ $c->telefono }} {{ $c->email }}</div>
                    </td>
                    <td class="small">
                        @if($c->vehiculo)
                            <a href="{{ route('vehiculos.show', $c->vehiculo) }}">{{ $c->vehiculo->descripcion_corta }}</a>
                        @else
                            consulta general
                        @endif
                    </td>
                    <td class="small">{{ Str::limit($c->mensaje, 90) }}</td>
                    <td><span class="badge badge-{{ $c->estado->color() }}">{{ $c->estado->etiqueta() }}</span></td>
                    <td class="text-right" style="width:190px">
                        <form method="POST" action="{{ route('consultas.actualizar', $c) }}" class="form-inline justify-content-end">
                            @csrf @method('PATCH')
                            <select name="estado" class="form-control form-control-sm mr-1">
                                @foreach($estados as $valor => $etiqueta)
                                    <option value="{{ $valor }}" @selected($c->estado->value === $valor)>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            <button class="btn btn-xs btn-primary">Guardar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No hay consultas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $consultas->links() }}</div>
</div>
@endsection
