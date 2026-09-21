<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        /*
         * Helvetica es una fuente PDF estandar y evita que Chrome muestre
         * glifos corruptos cuando interpreta la fuente DejaVu incrustada.
         * WinAnsi cubre correctamente los caracteres usados en espanol.
         */
        * { font-family: Helvetica, Arial, sans-serif; }
        body { font-size: 11px; color:#222; }
        h1 { font-size: 16px; margin:0 0 2px; }
        .encabezado { border-bottom: 2px solid #333; padding-bottom:8px; margin-bottom:14px; }
        .encabezado .negocio { font-size: 14px; font-weight: bold; }
        .encabezado .datos { color:#666; font-size:10px; }
        .numero { float:right; text-align:right; }
        .numero .valor { font-size:14px; font-weight:bold; }
        table { width:100%; border-collapse: collapse; margin-bottom:12px; }
        th, td { border:1px solid #ccc; padding:5px 6px; text-align:left; }
        th { background:#f0f0f0; }
        .sin-borde td { border:none; padding:2px 0; }
        .derecha { text-align:right; }
        .total { font-weight:bold; background:#f7f7f7; }
        .pie { position: fixed; bottom:0; width:100%; font-size:9px; color:#888; text-align:center; }
        .firma { margin-top:50px; }
        .firma td { border:none; border-top:1px solid #333; text-align:center; padding-top:4px; width:45%; }
    </style>
</head>
<body>
    <div class="encabezado">
        <div class="numero">
            <div>@yield('tipo-doc')</div>
            <div class="valor">@yield('numero-doc')</div>
        </div>
        <div class="negocio">{{ config('gestautos.negocio.nombre') }}</div>
        <div class="datos">
            RUC {{ config('gestautos.negocio.ruc') }} &bull; {{ config('gestautos.negocio.direccion') }}<br>
            {{ config('gestautos.negocio.telefono') }} &bull; {{ config('gestautos.negocio.email') }}
        </div>
    </div>

    @yield('contenido')

    <div class="pie">Documento generado por GestAutos el {{ now()->format('d/m/Y H:i') }}</div>
</body>
</html>
