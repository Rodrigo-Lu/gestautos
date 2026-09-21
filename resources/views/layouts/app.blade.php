<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titulo', 'Panel') | {{ config('gestautos.negocio.nombre') }}</title>

    {{-- AdminLTE 3 (incluye Bootstrap compilado) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
    <style>
        .filtro-responsive { gap: .5rem; }

        @media (max-width: 767.98px) {
            .content-header .container-fluid { padding-left: .75rem; padding-right: .75rem; }
            .content-header h1 { max-width: 65%; }
            .filtro-responsive { display: flex; align-items: stretch; }
            .filtro-responsive .form-control,
            .filtro-responsive .btn { margin-right: 0 !important; width: 100%; }
            .filtro-responsive .form-control { min-width: 0 !important; }
            .filtro-responsive .btn { white-space: nowrap; }
            .table-responsive { border: 0; }
            .table-responsive > .table { min-width: 680px; }
            .card-body.table-responsive { overflow-x: auto; }
        }
    </style>
    @stack('estilos')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    @include('partials.navbar')
    @include('partials.sidebar')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <h1 class="h4 mb-0">@yield('titulo', 'Panel')</h1>
                    <div>@yield('acciones')</div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                @include('partials.mensajes')
                @yield('contenido')
            </div>
        </section>
    </div>

    <footer class="main-footer text-sm">
        <strong>{{ config('gestautos.negocio.nombre') }}</strong> &mdash; GestAutos
        <div class="float-right d-none d-sm-inline">{{ now()->format('d/m/Y') }}</div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
@stack('scripts')
</body>
</html>
