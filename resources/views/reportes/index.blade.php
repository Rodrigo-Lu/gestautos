@extends('layouts.app')
@section('titulo', 'Reportes')

@section('contenido')
<div class="row">
    @foreach($tipos as $tipo)
        @continue(! Gate::allows('verTipo', [App\Models\Reporte::class, $tipo]))
        <div class="col-md-6 col-lg-3">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $tipo->etiqueta() }}</h3>
                    @if($tipo->esSensible())
                        <span class="badge badge-warning float-right">dirección</span>
                    @endif
                </div>
                <form method="GET" action="{{ route('reportes.generar', strtolower($tipo->value)) }}">
                    <div class="card-body">
                        <div class="form-group">
                            <label class="small">Desde</label>
                            <input type="date" name="desde" class="form-control form-control-sm"
                                   value="{{ now()->startOfMonth()->toDateString() }}">
                        </div>
                        <div class="form-group mb-0">
                            <label class="small">Hasta</label>
                            <input type="date" name="hasta" class="form-control form-control-sm"
                                   value="{{ now()->toDateString() }}">
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-sm btn-primary">Ver</button>
                        <button class="btn btn-sm btn-outline-secondary" name="pdf" value="1">PDF</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header"><h3 class="card-title">Últimos reportes generados</h3></div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Tipo</th><th>Periodo</th><th>Generado</th><th>Por</th></tr></thead>
            <tbody>
            @forelse($historial as $r)
                <tr>
                    <td>{{ $r->tipo->etiqueta() }}</td>
                    <td>{{ $r->fecha_desde?->format('d/m/Y') }} a {{ $r->fecha_hasta?->format('d/m/Y') }}</td>
                    <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $r->usuario->nombre }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Todavia no se genero ningun reporte.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
