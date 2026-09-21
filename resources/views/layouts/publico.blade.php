<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Vehículos usados') | {{ config('gestautos.negocio.nombre') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <style>
        body { background:#f6f7f9; }
        .card-vehiculo img { height: 190px; object-fit: cover; }
        .sin-foto { height:190px; display:flex; align-items:center; justify-content:center; background:#e9ecef; color:#adb5bd; }
        @media (max-width: 575.98px) {
            .container { --bs-gutter-x: 1rem; }
            .card-vehiculo img, .sin-foto { height: 165px; }
            .catalogo-filtros .row { --bs-gutter-x: .5rem; }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('catalogo.index') }}">{{ config('gestautos.negocio.nombre') }}</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('catalogo.index') }}">Vehículos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('permuta.create') }}">Vendé tu auto</a></li>
                @auth
                    @if(auth()->user()->rol === \App\Enums\Rol::CLIENTE)
                        <li class="nav-item"><a class="nav-link" href="{{ route('cliente.cuotas') }}">Mis cuotas</a></li>
                    @else
                        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Panel</a></li>
                    @endif
                @else
                    <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Ingresar</a></li>
                @endauth
            </ul>
        </div>
    </div>
</nav>

<main class="container py-4">
    @include('partials.mensajes')
    @yield('contenido')
</main>

<footer class="bg-dark text-white-50 py-4 mt-5">
    <div class="container small">
        <div class="row">
            <div class="col-md-6">
                <div class="text-white">{{ config('gestautos.negocio.nombre') }}</div>
                <div>{{ config('gestautos.negocio.direccion') }}</div>
            </div>
            <div class="col-md-6 text-md-end">
                <div>{{ config('gestautos.negocio.telefono') }}</div>
                <div>{{ config('gestautos.negocio.email') }}</div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
