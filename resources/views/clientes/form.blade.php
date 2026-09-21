@extends('layouts.app')
@section('titulo', $cliente->exists ? 'Editar cliente' : 'Nuevo cliente')

@section('contenido')
<div class="row">
    <div class="col-lg-6">
        <form method="POST" action="{{ $cliente->exists ? route('clientes.update', $cliente) : route('clientes.store') }}">
            @csrf
            @if($cliente->exists) @method('PUT') @endif

            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <label>Nombre y apellido</label>
                        <input type="text" name="nombre" class="form-control"
                               value="{{ old('nombre', $cliente->nombre) }}" required autofocus>
                    </div>
                    <div class="form-group">
                        <label>Cédula de identidad</label>
                        <input type="text" name="cedula" class="form-control"
                               value="{{ old('cedula', $cliente->cedula) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control"
                               value="{{ old('telefono', $cliente->telefono) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Correo electronico</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $cliente->email) }}">
                        <small class="text-muted">Necesario si despues le vas a dar acceso al portal.</small>
                    </div>
                    <div class="form-group mb-0">
                        <label>Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                               value="{{ old('direccion', $cliente->direccion) }}">
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Guardar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-link">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
