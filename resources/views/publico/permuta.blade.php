@extends('layouts.publico')
@section('titulo', 'Entregá tu vehículo')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h1 class="h4">Entregá tu vehículo como parte de pago</h1>
        <p class="text-muted">Cargá los datos y te llamamos con una oferta. No hace falta crear una cuenta.</p>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('permuta.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tu nombre</label>
                            <input type="text" name="nombre_contacto" class="form-control"
                                   value="{{ old('nombre_contacto') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono_contacto" class="form-control"
                                   value="{{ old('telefono_contacto') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Marca</label>
                            <input type="text" name="marca" class="form-control" value="{{ old('marca') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Modelo</label>
                            <input type="text" name="modelo" class="form-control" value="{{ old('modelo') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Año</label>
                            <input type="number" name="anio" class="form-control" value="{{ old('anio') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kilometraje</label>
                            <input type="number" name="kilometraje" class="form-control" value="{{ old('kilometraje') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Estado general, detalles, service</label>
                            <textarea name="observaciones" rows="3" class="form-control">{{ old('observaciones') }}</textarea>
                        </div>
                    </div>
                    <button class="btn btn-dark mt-3">Pedir oferta</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
