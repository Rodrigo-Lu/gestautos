@extends('layouts.app')
@section('titulo', 'Alertas de cuotas')

@section('acciones')
    <form method="POST" action="{{ route('alertas.todas') }}" class="d-inline">
        @csrf
        <button class="btn btn-sm btn-outline-secondary">Archivar todas</button>
    </form>
@endsection

@section('contenido')
<div class="card">
    <div class="card-header">
        <div class="btn-group">
            <a href="{{ route('alertas.index') }}" class="btn btn-sm btn-{{ $vista !== 'todas' ? 'primary' : 'outline-primary' }}">Pendientes</a>
            <a href="{{ route('alertas.index', ['vista' => 'todas']) }}" class="btn btn-sm btn-{{ $vista === 'todas' ? 'primary' : 'outline-primary' }}">Historial</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <tbody>
            @forelse($alertas as $a)
                <tr class="{{ $a->leida ? 'text-muted' : '' }}">
                    <td style="width:120px">
                        <span class="badge badge-{{ $a->tipo->color() }}">{{ $a->tipo->etiqueta() }}</span>
                    </td>
                    <td style="width:110px">{{ $a->fecha_generacion->format('d/m/Y') }}</td>
                    <td>{{ $a->mensaje }}</td>
                    <td class="text-right">
                        @if($a->cuota)
                            <a href="{{ route('cuotas.cobrar', $a->cuota) }}" class="btn btn-xs btn-primary">Cobrar</a>
                        @endif
                        @unless($a->leida)
                            <form method="POST" action="{{ route('alertas.leida', $a) }}" class="d-inline">
                                @csrf
                                <button class="btn btn-xs btn-outline-secondary">Archivar</button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @empty
                <tr><td class="text-center text-muted py-4">
                    No hay alertas pendientes. El comando diario las genera a las 07:00.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $alertas->links() }}</div>
</div>
@endsection
