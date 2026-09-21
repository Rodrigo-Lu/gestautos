@extends('layouts.app')
@section('titulo', 'Clientes')

@section('acciones')
    <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm">Nuevo cliente</a>
@endsection

@section('contenido')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline filtro-responsive">
            <input type="text" name="q" value="{{ $q }}" class="form-control mr-2"
                   placeholder="Nombre, cédula, teléfono o correo" style="min-width:280px">
            <button class="btn btn-primary">Buscar</button>
            @if($q)<a href="{{ route('clientes.index') }}" class="btn btn-link">Limpiar</a>@endif
        </form>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Compras</th><th>Portal</th><th></th></tr></thead>
            <tbody>
            @forelse($clientes as $c)
                <tr>
                    <td><a href="{{ route('clientes.show', $c) }}">{{ $c->nombre }}</a></td>
                    <td>{{ $c->cedula }}</td>
                    <td>{{ $c->telefono }}</td>
                    <td>{{ $c->ventas_count }}</td>
                    <td>
                        @if($c->tieneAcceso())
                            <span class="badge badge-success">Con acceso</span>
                        @else
                            <span class="text-muted small">sin acceso</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('clientes.edit', $c) }}" class="btn btn-xs btn-outline-secondary">Editar</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">
                    @if($q) No encontramos clientes con ese dato. @else Todavía no hay clientes cargados. @endif
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $clientes->links() }}</div>
</div>
@endsection
