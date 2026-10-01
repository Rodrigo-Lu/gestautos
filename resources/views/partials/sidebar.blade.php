@php($u = auth()->user())
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ $u->esAdministrativo() ? route('dashboard') : route('vehiculos.index') }}" class="brand-link text-center">
        <span class="brand-text font-weight-light">GestAutos</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">

                @if($u->esAdministrativo())
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i><p>Inicio</p>
                        </a>
                    </li>
                @endif

                <li class="nav-header">OPERACIÓN</li>

                <li class="nav-item">
                    <a href="{{ route('vehiculos.index') }}" class="nav-link {{ request()->routeIs('vehiculos.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-car"></i><p>Vehículos</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i><p>Clientes</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ventas.index') }}" class="nav-link {{ request()->routeIs('ventas.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-signature"></i><p>Ventas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('cuotas.index') }}" class="nav-link {{ request()->routeIs('cuotas.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-hand-holding-usd"></i><p>Cobranzas</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('alertas.index') }}" class="nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}">
                        <i class="nav-icon far fa-bell"></i><p>Alertas</p>
                    </a>
                </li>

                <li class="nav-header">ATENCION</li>

                <li class="nav-item">
                    <a href="{{ route('consultas.index') }}" class="nav-link {{ request()->routeIs('consultas.*') ? 'active' : '' }}">
                        <i class="nav-icon far fa-comments"></i><p>Consultas web</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('tasaciones.index') }}" class="nav-link {{ request()->routeIs('tasaciones.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-exchange-alt"></i><p>Permutas</p>
                    </a>
                </li>

                <li class="nav-header">GESTION</li>

                <li class="nav-item">
                    <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-alt"></i><p>Reportes</p>
                    </a>
                </li>

                @if($u->esAdministrativo())
                    <li class="nav-item">
                        <a href="{{ route('usuarios.index') }}" class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-shield"></i><p>Usuarios</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
