@extends('layouts.app')
@section('titulo', 'Dashboard')

@section('acciones')
    <a href="{{ route('ventas.create') }}" class="btn btn-primary btn-sm">Registrar venta</a>
@endsection

@section('contenido')
<div class="row">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h4 class="mb-2">Ventas del mes</h4>
                <div>{{ \App\Enums\Moneda::GS->formatear($ventasMesPorMoneda['GS']) }}</div>
                <div>{{ \App\Enums\Moneda::USD->formatear($ventasMesPorMoneda['USD']) }}</div>
                <small>{{ $ventasMesCantidad }} operaciones</small>
            </div>
            <a href="{{ route('ventas.index') }}" class="small-box-footer">Ver ventas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h4 class="mb-2">Vehículos disponibles</h4>
                <h3>{{ $disponibles }}</h3>
            </div>
            <a href="{{ route('vehiculos.index', ['estado' => 'DISPONIBLE']) }}" class="small-box-footer">Ver stock <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h4 class="mb-2">Cuotas vencidas</h4>
                <h3>{{ $cuotasVencidas }}</h3>
            </div>
            <a href="{{ route('cuotas.index', ['vista' => 'vencidas']) }}" class="small-box-footer">Ir a cobranzas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h4 class="mb-2">Total por cobrar</h4>
                <div>{{ \App\Enums\Moneda::GS->formatear($montoPorCobrar['GS']) }}</div>
                <div>{{ \App\Enums\Moneda::USD->formatear($montoPorCobrar['USD']) }}</div>
                <small>Saldo de cuotas abiertas</small>
            </div>
            <a href="{{ route('cuotas.index') }}" class="small-box-footer">Ver cobranzas <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Ventas de los últimos 6 meses</h3></div>
            <div class="card-body"><div class="chart-container" style="height: 300px"><canvas id="ventasPorMes"></canvas></div></div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Stock por estado</h3></div>
            <div class="card-body"><div class="chart-container" style="height: 300px"><canvas id="stockPorEstado"></canvas></div></div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Últimas 5 ventas</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Fecha</th><th>Cliente</th><th>Vehículo</th><th class="text-right">Importe</th></tr></thead>
                    <tbody>
                    @forelse($ultimasVentas as $venta)
                        <tr>
                            <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td>{{ $venta->cliente->nombre }}</td>
                            <td>{{ $venta->vehiculo->descripcion_corta }}</td>
                            <td class="text-right">{{ $venta->moneda->formatear($venta->precio_venta) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Todavía no hay ventas cargadas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h3 class="card-title">Próximas 5 cuotas por vencer</h3></div>
            <div class="card-body table-responsive p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>Vence</th><th>Cliente</th><th>Cuota</th><th class="text-right">Saldo</th></tr></thead>
                    <tbody>
                    @forelse($proximasCuotas as $cuota)
                        @php($venta = $cuota->financiamiento->venta)
                        <tr>
                            <td>{{ $cuota->fecha_vencimiento->format('d/m/Y') }}</td>
                            <td>
                                <div>{{ $venta->cliente->nombre }}</div>
                                <small class="text-muted">{{ $venta->vehiculo->descripcion_corta }}</small>
                            </td>
                            <td>{{ $cuota->numero_cuota }}/{{ $cuota->financiamiento->cantidad_cuotas }}</td>
                            <td class="text-right">{{ $venta->moneda->formatear($cuota->saldo) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">No hay cuotas próximas.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>
<script>
    const monedaTooltip = (value, moneda) => {
        const decimales = moneda === 'Gs.' ? 0 : 2;
        return moneda + ' ' + new Intl.NumberFormat('es-PY', {
            minimumFractionDigits: decimales,
            maximumFractionDigits: decimales,
        }).format(value);
    };

    new Chart(document.getElementById('ventasPorMes'), {
        type: 'bar',
        data: {
            labels: @json($mesesLabels),
            datasets: [
                {
                    label: 'Gs.',
                    data: @json($ventasPorMesGs),
                    backgroundColor: '#007bff',
                    borderRadius: 4,
                },
                {
                    label: 'USD',
                    data: @json($ventasPorMesUsd),
                    backgroundColor: '#17a2b8',
                    borderRadius: 4,
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            scales: {
                y: { beginAtZero: true, ticks: { callback: value => new Intl.NumberFormat('es-PY').format(value) } },
            },
            plugins: {
                tooltip: { callbacks: { label: context => ' ' + monedaTooltip(context.raw, context.dataset.label) } },
            },
        },
    });

    new Chart(document.getElementById('stockPorEstado'), {
        type: 'doughnut',
        data: {
            labels: @json($stockLabels),
            datasets: [{
                data: @json($stockData),
                backgroundColor: @json($stockColors),
                borderWidth: 2,
                borderColor: '#fff',
            }],
        },
        options: {
            maintainAspectRatio: false,
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
        },
    });
</script>
@endpush
