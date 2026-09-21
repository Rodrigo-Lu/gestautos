@extends('layouts.publico')
@section('titulo', $vehiculo->descripcion_corta)

@section('contenido')
<nav class="mb-3"><a href="{{ route('catalogo.index') }}" class="small text-decoration-none">&larr; Volver al catalogo</a></nav>

<div class="row">
    <div class="col-lg-7">
        <div class="card mb-4">
            @if($vehiculo->fotos->isNotEmpty())
                <div id="galeria" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($vehiculo->fotos as $i => $foto)
                            <div class="carousel-item @if($i === 0) active @endif">
                                <img src="{{ $foto->url }}" class="d-block w-100" alt="">
                            </div>
                        @endforeach
                    </div>
                    @if($vehiculo->fotos->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#galeria" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#galeria" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            @else
                <div class="sin-foto"><i class="fas fa-car fa-3x"></i></div>
            @endif
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <h2 class="h6">Ficha tecnica</h2>
                <div class="row small">
                    <div class="col-6 col-md-4 mb-2"><span class="text-muted">Año</span><br>{{ $vehiculo->anio }}</div>
                    <div class="col-6 col-md-4 mb-2"><span class="text-muted">Kilometraje</span><br>{{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</div>
                    <div class="col-6 col-md-4 mb-2"><span class="text-muted">Transmisión</span><br>{{ $vehiculo->transmision->etiqueta() }}</div>
                    <div class="col-6 col-md-4 mb-2"><span class="text-muted">Combustible</span><br>{{ $vehiculo->combustible->etiqueta() }}</div>
                    <div class="col-6 col-md-4 mb-2"><span class="text-muted">Color</span><br>{{ $vehiculo->color ?: '-' }}</div>
                    <div class="col-6 col-md-4 mb-2"><span class="text-muted">Permuta</span><br>{{ $vehiculo->acepta_permuta ? 'Acepta' : 'No acepta' }}</div>
                </div>
                @if($vehiculo->descripcion)
                    <hr><p class="small mb-0">{{ $vehiculo->descripcion }}</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card mb-4">
            <div class="card-body">
                <h1 class="h4 mb-1">{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h1>
                <p class="text-muted">{{ $vehiculo->version }} {{ $vehiculo->anio }}</p>
                <p class="h3 mb-3">{{ $vehiculo->precioFormateado() }}</p>

                <form method="POST" action="{{ route('simulador') }}" class="border-top pt-3">
                    @csrf
                    <input type="hidden" name="vehiculo_id" value="{{ $vehiculo->id }}">
                    <h2 class="h6">Simulá tus cuotas</h2>
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small">Entrega inicial</label>
                            <input type="number" name="anticipo" class="form-control form-control-sm"
                                   value="{{ (int) ($vehiculo->precio * 0.2) }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Cuotas</label>
                            <select name="cantidad_cuotas" class="form-select form-select-sm">
                                @foreach($plazos as $p)
                                    <option value="{{ $p }}" @selected($p === 12)>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-dark btn-sm w-100">Calcular</button>
                    <p class="small text-muted mt-2 mb-0">Tasa referencial {{ $tasa }}% mensual. Sujeto a aprobacion.</p>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h2 class="h6">Consultar por este vehículo</h2>
                <form method="POST" action="{{ route('consultas.publicas.store') }}">
                    @csrf
                    <input type="hidden" name="vehiculo_id" value="{{ $vehiculo->id }}">
                    <div class="mb-2">
                        <input type="text" name="nombre" class="form-control form-control-sm"
                               placeholder="Tu nombre" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="mb-2">
                        <input type="text" name="telefono" class="form-control form-control-sm"
                               placeholder="Teléfono" value="{{ old('telefono') }}" required>
                    </div>
                    <div class="mb-2">
                        <input type="email" name="email" class="form-control form-control-sm"
                               placeholder="Correo (opcional)" value="{{ old('email') }}">
                    </div>
                    <div class="mb-2">
                        <textarea name="mensaje" rows="3" class="form-control form-control-sm"
                                  placeholder="Tu consulta" required>{{ old('mensaje') }}</textarea>
                    </div>
                    <button class="btn btn-outline-dark btn-sm w-100">Enviar consulta</button>
                </form>
            </div>
        </div>
    </div>
</div>

@if($similares->isNotEmpty())
    <h2 class="h5 mt-4 mb-3">Otros {{ $vehiculo->marca }}</h2>
    <div class="row">
        @foreach($similares as $s)
            <div class="col-sm-4 mb-3">
                <div class="card h-100 card-vehiculo">
                    @if($s->fotoPrincipal)
                        <img src="{{ $s->fotoPrincipal->url }}" class="card-img-top" alt="">
                    @else
                        <div class="sin-foto"><i class="fas fa-car fa-2x"></i></div>
                    @endif
                    <div class="card-body">
                        <h3 class="h6 mb-1">{{ $s->marca }} {{ $s->modelo }} {{ $s->anio }}</h3>
                        <p class="mb-0">{{ $s->precioFormateado() }}</p>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('catalogo.show', $s) }}" class="btn btn-sm btn-outline-dark w-100">Ver</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
