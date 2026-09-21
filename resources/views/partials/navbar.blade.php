<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('catalogo.index') }}" class="nav-link" target="_blank">Ver el sitio publico</a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('alertas.index') }}" title="Alertas de cuotas">
                <i class="far fa-bell"></i>
                @if(($alertasNoLeidas ?? 0) > 0)
                    <span class="badge badge-danger navbar-badge">{{ $alertasNoLeidas }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                {{ auth()->user()->nombre }}
                <span class="badge badge-secondary ml-1">{{ auth()->user()->rol->etiqueta() }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Cerrar sesion</button>
                </form>
            </div>
        </li>
    </ul>
</nav>
