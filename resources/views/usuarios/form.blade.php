@extends('layouts.app')
@section('titulo', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('contenido')
<div class="row">
    <div class="col-lg-6">
        <form method="POST" action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}">
            @csrf
            @if($usuario->exists) @method('PUT') @endif

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ old('nombre', $usuario->nombre) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Correo electronico</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $usuario->email) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Rol</label>
                        <select name="rol" class="form-control">
                            @foreach($roles as $valor => $etiqueta)
                                <option value="{{ $valor }}"
                                    @selected(old('rol', $usuario->rol?->value ?? 'EMPLEADO') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control"
                               @required(! $usuario->exists)>
                        @if($usuario->exists)
                            <small class="text-muted">Dejalo vacio para no cambiarla.</small>
                        @endif
                    </div>
                    <div class="form-group">
                        <label>Repetir contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control"
                               @required(! $usuario->exists)>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1"
                               @checked(old('activo', $usuario->activo ?? true))>
                        <label class="custom-control-label" for="activo">Puede iniciar sesion</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Guardar</button>
                    <a href="{{ route('usuarios.index') }}" class="btn btn-link">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
