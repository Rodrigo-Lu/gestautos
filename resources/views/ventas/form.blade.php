@extends('layouts.app')
@section('titulo', 'Registrar venta')

@section('contenido')
<form method="POST" action="{{ route('ventas.store') }}" id="form-venta">
    @csrf
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Operación</h3></div>
                <div class="card-body row">
                    <div class="form-group col-md-8">
                        <label>Vehículo</label>
                        <select name="vehiculo_id" id="vehiculo" class="form-control" required>
                            <option value="">Elegí el vehículo</option>
                            @foreach($vehiculos as $v)
                                <option value="{{ $v->id }}" data-precio="{{ $v->precio }}" data-moneda="{{ $v->moneda->value }}"
                                    @selected(old('vehiculo_id', $preseleccion) == $v->id)>
                                    {{ $v->codigo_publicacion }} &mdash; {{ $v->descripcion_corta }} ({{ $v->precioFormateado() }})
                                </option>
                            @endforeach
                        </select>
                        @if($vehiculos->isEmpty())
                            <small class="text-danger">No hay vehículos disponibles en el stock.</small>
                        @endif
                    </div>
                    <div class="form-group col-md-4">
                        <label>Fecha</label>
                        <input type="date" name="fecha" class="form-control"
                               value="{{ old('fecha', now()->toDateString()) }}" required>
                    </div>

                    <div class="form-group col-md-8">
                        <label>Cliente</label>
                        <select name="cliente_id" class="form-control" required>
                            <option value="">Elegí el cliente</option>
                            @foreach($clientes as $c)
                                <option value="{{ $c->id }}" @selected(old('cliente_id') == $c->id)>
                                    {{ $c->nombre }} &mdash; CI {{ $c->cedula }}
                                </option>
                            @endforeach
                        </select>
                        <small><a href="{{ route('clientes.create') }}" target="_blank">Cargar un cliente nuevo</a></small>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Tipo de venta</label>
                        <select name="tipo_venta" id="tipo-venta" class="form-control" required>
                            @foreach($tipos as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('tipo_venta') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Moneda</label>
                        <select name="moneda" id="moneda" class="form-control">
                            @foreach($monedas as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('moneda', 'GS') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Precio pactado</label>
                        <input type="number" step="0.01" name="precio_venta" id="precio" class="form-control"
                               value="{{ old('precio_venta') }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Cotizacion del dia</label>
                        <input type="number" step="0.0001" name="tipo_cambio" class="form-control"
                               value="{{ old('tipo_cambio') }}" placeholder="solo si la venta es en USD">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Permuta recibida</label>
                        <select name="tasacion_permuta_id" class="form-control">
                            <option value="">Sin permuta</option>
                            @foreach($tasaciones as $t)
                                <option value="{{ $t->id }}" @selected(old('tasacion_permuta_id') == $t->id)>
                                    {{ $t->descripcion }} &mdash; {{ $t->nombre_contacto }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Valor de la permuta</label>
                        <input type="number" step="0.01" name="monto_permuta" class="form-control"
                               value="{{ old('monto_permuta', 0) }}">
                    </div>

                    <div class="form-group col-12">
                        <label>Observaciones</label>
                        <textarea name="observaciones" rows="2" class="form-control">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card" id="bloque-financiamiento">
                <div class="card-header"><h3 class="card-title">Financiamiento</h3></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Forma de pago del anticipo</label>
                        <select name="forma_pago_anticipo" class="form-control">
                            @foreach($formasPago as $valor => $etiqueta)
                                <option value="{{ $valor }}" @selected(old('forma_pago_anticipo') === $valor)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Anticipo</label>
                        <input type="number" step="0.01" name="monto_anticipo" class="form-control"
                               value="{{ old('monto_anticipo', 0) }}">
                        <small class="text-muted">Sugerido: {{ config('gestautos.financiamiento.anticipo_minimo_pct') }}% del precio.</small>
                    </div>
                    <div class="form-group">
                        <label>Cantidad de cuotas</label>
                        <select name="cantidad_cuotas" class="form-control">
                            @foreach($plazos as $p)
                                <option value="{{ $p }}" @selected(old('cantidad_cuotas', 12) == $p)>{{ $p }} cuotas</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Interés mensual (%)</label>
                        <input type="number" step="0.01" name="tasa_interes_mensual" class="form-control"
                               value="{{ old('tasa_interes_mensual', $tasaDefault) }}">
                    </div>
                    <div class="form-group">
                        <label>Mora diaria (%)</label>
                        <input type="number" step="0.01" name="tasa_mora_diaria" class="form-control"
                               value="{{ old('tasa_mora_diaria', $moraDefault) }}">
                    </div>
                    <div class="form-group mb-0">
                        <label>Primer vencimiento</label>
                        <input type="date" name="fecha_primer_vencimiento" class="form-control"
                               value="{{ old('fecha_primer_vencimiento', now()->addMonth()->toDateString()) }}">
                    </div>
                </div>
            </div>

            <button class="btn btn-primary btn-block">Registrar venta y emitir comprobante</button>
            <a href="{{ route('ventas.index') }}" class="btn btn-link btn-block">Cancelar</a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const tipo  = document.getElementById('tipo-venta');
    const bloque= document.getElementById('bloque-financiamiento');
    const auto  = document.getElementById('vehiculo');
    const precio= document.getElementById('precio');
    const moneda= document.getElementById('moneda');

    function alternarFinanciamiento() {
        bloque.style.display = tipo.value === 'FINANCIADO' ? '' : 'none';
    }

    // Al elegir vehiculo, precarga el precio de lista: el vendedor lo puede pisar.
    auto.addEventListener('change', function () {
        const op = this.selectedOptions[0];
        if (!op || !op.dataset.precio) return;
        precio.value  = op.dataset.precio;
        moneda.value  = op.dataset.moneda;
    });

    tipo.addEventListener('change', alternarFinanciamiento);
    alternarFinanciamiento();
</script>
@endpush
