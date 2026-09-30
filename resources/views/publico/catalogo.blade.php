@extends('layouts.publico')
@section('titulo', 'Vehículos disponibles')

@section('contenido')
<div class="row">
    <aside class="col-lg-3 mb-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h6 mb-3">Buscar</h2>
                <form method="GET" class="catalogo-filtros">
                    <div class="mb-3">
                        <input type="text" name="q" value="{{ $filtros['q'] ?? '' }}"
                               class="form-control form-control-sm" placeholder="Marca o modelo">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Marca</label>
                        <select name="marca" class="form-select form-select-sm">
                            <option value="">Todas</option>
                            @foreach($marcas as $m)
                                <option value="{{ $m }}" @selected(($filtros['marca'] ?? '') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small">Año desde</label>
                            <input type="number" name="anio_desde" value="{{ $filtros['anio_desde'] ?? '' }}"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Año hasta</label>
                            <input type="number" name="anio_hasta" value="{{ $filtros['anio_hasta'] ?? '' }}"
                                   class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small">Precio desde</label>
                            <input type="number" name="precio_desde" value="{{ $filtros['precio_desde'] ?? '' }}"
                                   class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Precio hasta</label>
                            <input type="number" name="precio_hasta" value="{{ $filtros['precio_hasta'] ?? '' }}"
                                   class="form-control form-control-sm">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small">Combustible</label>
                        <select name="combustible" class="form-select form-select-sm">
                            <option value="">Cualquiera</option>
                            @foreach($combustibles as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(($filtros['combustible'] ?? '') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-dark btn-sm w-100">Filtrar</button>
                    <a href="{{ route('catalogo.index') }}" class="btn btn-link btn-sm w-100">Limpiar filtros</a>
                </form>
            </div>
        </div>

        <div class="card mt-3 bg-dark text-white">
            <div class="card-body">
                <h2 class="h6">Tenes un auto para entregar?</h2>
                <p class="small mb-3">Cargá los datos y te pasamos una oferta de permuta.</p>
                <a href="{{ route('permuta.create') }}" class="btn btn-light btn-sm">Ofrecer mi vehículo</a>
            </div>
        </div>
    </aside>

    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">{{ $vehiculos->total() }} vehículos disponibles</h1>
        </div>

        <div class="row">
            @forelse($vehiculos as $v)
                <div class="col-sm-6 col-lg-4 mb-4">
                    <div class="card h-100 card-vehiculo">
                        @if($v->fotoPrincipal)
                            <img src="{{ $v->fotoPrincipal->url }}" class="card-img-top" alt="{{ $v->descripcion_corta }}">
                        @else
                            <div class="sin-foto"><i class="fas fa-car fa-2x"></i></div>
                        @endif
                        <div class="card-body">
                            <h2 class="h6 mb-1">{{ $v->marca }} {{ $v->modelo }}</h2>
                            <p class="small text-muted mb-2">
                                {{ $v->anio }} &bull; {{ number_format($v->kilometraje, 0, ',', '.') }} km
                                &bull; {{ $v->transmision->etiqueta() }}
                            </p>
                            <p class="h6 mb-0">{{ $v->precioFormateado() }}</p>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <a href="{{ route('catalogo.show', $v) }}" class="btn btn-dark btn-sm w-100 mb-2">Ver detalle</a>
                            @if($v->whatsappLink())
                                <a href="{{ config('services.whatsapp.registrar_consulta') ? route('whatsapp.redirect', $v) : $v->whatsappLink() }}"
                                   class="btn btn-whatsapp btn-sm w-100"
                                   target="_blank" rel="noopener">
                                    <i class="fa-brands fa-whatsapp me-1" aria-hidden="true"></i>
                                    Consultar por WhatsApp
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border text-center py-5">
                        No hay vehículos que coincidan con la búsqueda. Probá quitando algún filtro.
                    </div>
                </div>
            @endforelse
        </div>

        {{ $vehiculos->links() }}
    </div>
</div>
@endsection
