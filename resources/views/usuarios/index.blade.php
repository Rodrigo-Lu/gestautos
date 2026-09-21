@extends('layouts.app')
@section('titulo', 'Usuarios')

@section('acciones')
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary btn-sm">Nuevo usuario</a>
@endsection

@section('contenido')
<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <select name="rol" class="form-control mr-2" onchange="this.form.submit()">
                <option value="">Todos los roles</option>
                @foreach($roles as $valor => $etiqueta)
                    <option value="{{ $valor }}" @selected($rol === $valor)>{{ $etiqueta }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Estado</th><th></th></tr></thead>
            <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td>{{ $u->nombre }}</td>
                    <td>{{ $u->email }}</td>
                    <td>{{ $u->rol->etiqueta() }}</td>
                    <td>
                        @if($u->activo)
                            <span class="badge badge-success">Activo</span>
                        @else
                            <span class="badge badge-secondary">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-right">
                        <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-xs btn-outline-secondary">Editar</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">{{ $usuarios->links() }}</div>
</div>
@endsection
