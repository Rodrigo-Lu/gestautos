<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ingresar | {{ config('gestautos.negocio.nombre') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container" style="max-width:420px; margin-top:8vh;">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h1 class="h4 mb-1">GestAutos</h1>
            <p class="text-muted small mb-4">{{ config('gestautos.negocio.nombre') }}</p>

            @if($errors->any())
                <div class="alert alert-danger py-2 small">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electronico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" id="remember" name="remember" class="form-check-input">
                    <label for="remember" class="form-check-label small">Mantener sesion iniciada</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Ingresar</button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('catalogo.index') }}" class="small text-muted">Volver al catalogo</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
