@extends('layouts.app')
@section('titulo', $vehiculo->exists ? 'Editar vehículo' : 'Cargar vehículo')

@section('contenido')
<form method="POST"
      action="{{ $vehiculo->exists ? route('vehiculos.update', $vehiculo) : route('vehiculos.store') }}"
      enctype="multipart/form-data">
    @csrf
    @if($vehiculo->exists) @method('PUT') @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Datos del vehículo</h3></div>
                <div class="card-body row">
                    <div class="form-group col-md-4">
                        <label>Código de publicación</label>
                        <input type="text" name="codigo_publicacion" class="form-control"
                               value="{{ old('codigo_publicacion', $vehiculo->codigo_publicacion) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Marca</label>
                        <input type="text" name="marca" class="form-control"
                               value="{{ old('marca', $vehiculo->marca) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Modelo</label>
                        <input type="text" name="modelo" class="form-control"
                               value="{{ old('modelo', $vehiculo->modelo) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Version</label>
                        <input type="text" name="version" class="form-control"
                               value="{{ old('version', $vehiculo->version) }}">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Año</label>
                        <input type="number" name="anio" class="form-control"
                               value="{{ old('anio', $vehiculo->anio) }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Kilometraje</label>
                        <input type="number" name="kilometraje" class="form-control"
                               value="{{ old('kilometraje', $vehiculo->kilometraje ?? 0) }}" required>
                    </div>
                    <div class="form-group col-md-3">
                        <label>Color</label>
                        <input type="text" name="color" class="form-control"
                               value="{{ old('color', $vehiculo->color) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Número de chasis</label>
                        <input type="text" name="numero_chasis" class="form-control"
                               value="{{ old('numero_chasis', $vehiculo->numero_chasis) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Transmisión</label>
                        <select name="transmision" class="form-control">
                            @foreach($transmisiones as $valor => $etiqueta)
                                <option value="{{ $valor }}"
                                    @selected(old('transmision', $vehiculo->transmision?->value) === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Combustible</label>
                        <select name="combustible" class="form-control">
                            @foreach($combustibles as $valor => $etiqueta)
                                <option value="{{ $valor }}"
                                    @selected(old('combustible', $vehiculo->combustible?->value) === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-12">
                        <label>Descripcion para el catalogo</label>
                        <textarea name="descripcion" rows="3" class="form-control">{{ old('descripcion', $vehiculo->descripcion) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Fotografias</h3></div>
                <div class="card-body">
                    <input type="file" name="fotos[]" class="form-control-file" accept="image/*" multiple>
                    <small class="text-muted">Hasta {{ config('gestautos.catalogo.fotos_por_vehiculo') }} fotos, 4 MB cada una.</small>

                    @if($vehiculo->exists && $vehiculo->fotos->isNotEmpty())
                        <div class="row mt-3">
                            @foreach($vehiculo->fotos as $foto)
                                <div class="col-6 col-md-3 mb-3">
                                    <img src="{{ $foto->url }}" class="img-fluid rounded mb-1" alt="">
                                    @if($foto->es_principal)
                                        <span class="badge badge-success">Principal</span>
                                    @else
                                        <button type="submit" class="btn btn-xs btn-outline-secondary"
                                                formaction="{{ route('vehiculos.fotos.principal', [$vehiculo, $foto]) }}"
                                                formmethod="POST">Hacer principal</button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Comercial</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Moneda</label>
                        <select name="moneda" class="form-control">
                            @foreach($monedas as $valor => $etiqueta)
                                <option value="{{ $valor }}"
                                    @selected(old('moneda', $vehiculo->moneda?->value ?? 'GS') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Precio de venta</label>
                        <input type="number" step="0.01" name="precio" class="form-control"
                               value="{{ old('precio', $vehiculo->precio) }}" required>
                    </div>

                    @can('verCosto', App\Models\Vehiculo::class)
                        <div class="form-group">
                            <label>Precio de compra</label>
                            <input type="number" step="0.01" name="precio_compra" class="form-control"
                                   value="{{ old('precio_compra', $vehiculo->precio_compra) }}">
                            <small class="text-muted">Solo visible para la direccion. Necesario para el reporte de rentabilidad.</small>
                        </div>
                    @endcan

                    <div class="form-group">
                        <label>Fecha de ingreso</label>
                        <input type="date" name="fecha_ingreso" class="form-control"
                               value="{{ old('fecha_ingreso', optional($vehiculo->fecha_ingreso)->format('Y-m-d')) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Estado</label>
                        <select name="estado" class="form-control">
                            @foreach($estados as $valor => $etiqueta)
                                <option value="{{ $valor }}"
                                    @selected(old('estado', $vehiculo->estado?->value ?? 'DISPONIBLE') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" class="custom-control-input" id="permuta" name="acepta_permuta" value="1"
                               @checked(old('acepta_permuta', $vehiculo->acepta_permuta))>
                        <label class="custom-control-label" for="permuta">Acepta permuta</label>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="activo" name="activo" value="1"
                               @checked(old('activo', $vehiculo->activo ?? true))>
                        <label class="custom-control-label" for="activo">Publicado en el catalogo</label>
                    </div>
                </div>
                <div class="card-footer">
                    <button class="btn btn-primary">Guardar</button>
                    <a href="{{ route('vehiculos.index') }}" class="btn btn-link">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
