<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Enums\Moneda;
use App\Enums\TipoCombustible;
use App\Enums\TipoTransmision;
use App\Http\Requests\VehiculoRequest;
use App\Models\FotoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehiculoController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Vehiculo::class, 'vehiculo');
    }

    public function index(Request $request)
    {
        $filtros = $request->only([
            'q', 'marca', 'estado', 'anio_desde', 'anio_hasta',
            'precio_desde', 'precio_hasta', 'combustible', 'transmision',
        ]);

        $vehiculos = Vehiculo::with('fotoPrincipal')
            ->filtrar($filtros)
            ->orderByDesc('fecha_ingreso')
            ->paginate(15)
            ->withQueryString();

        return view('vehiculos.index', [
            'vehiculos' => $vehiculos,
            'filtros'   => $filtros,
            'marcas'    => Vehiculo::query()->distinct()->orderBy('marca')->pluck('marca'),
            'estados'   => EstadoVehiculo::opciones(),
        ]);
    }

    public function create()
    {
        return view('vehiculos.form', [
            'vehiculo'     => new Vehiculo(['fecha_ingreso' => now(), 'moneda' => Moneda::GS]),
            'monedas'      => Moneda::opciones(),
            'estados'      => EstadoVehiculo::opciones(),
            'transmisiones'=> TipoTransmision::opciones(),
            'combustibles' => TipoCombustible::opciones(),
        ]);
    }

    public function store(VehiculoRequest $request)
    {
        $vehiculo = DB::transaction(function () use ($request) {
            $vehiculo = Vehiculo::create($request->safe()->except('fotos'));
            $this->guardarFotos($vehiculo, $request);

            return $vehiculo;
        });

        return redirect()
            ->route('vehiculos.show', $vehiculo)
            ->with('exito', 'Vehículo registrado.');
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load(['fotos', 'ventas.cliente', 'consultas', 'tasacionOrigen']);

        return view('vehiculos.show', compact('vehiculo'));
    }

    public function edit(Vehiculo $vehiculo)
    {
        $vehiculo->load('fotos');

        return view('vehiculos.form', [
            'vehiculo'     => $vehiculo,
            'monedas'      => Moneda::opciones(),
            'estados'      => EstadoVehiculo::opciones(),
            'transmisiones'=> TipoTransmision::opciones(),
            'combustibles' => TipoCombustible::opciones(),
        ]);
    }

    public function update(VehiculoRequest $request, Vehiculo $vehiculo)
    {
        DB::transaction(function () use ($request, $vehiculo) {
            $vehiculo->update($request->safe()->except('fotos'));
            $this->guardarFotos($vehiculo, $request);
        });

        return redirect()
            ->route('vehiculos.show', $vehiculo)
            ->with('exito', 'Cambios guardados.');
    }

    public function destroy(Vehiculo $vehiculo)
    {
        if ($vehiculo->ventas()->exists()) {
            return back()->with('error', 'Ese vehículo tiene ventas registradas. Márcalo como inactivo en lugar de eliminarlo.');
        }

        $vehiculo->delete();

        return redirect()
            ->route('vehiculos.index')
            ->with('exito', 'Vehículo dado de baja. Queda en el historial y se puede restaurar.');
    }

    private function guardarFotos(Vehiculo $vehiculo, VehiculoRequest $request): void
    {
        if (! $request->hasFile('fotos')) {
            return;
        }

        $orden = (int) $vehiculo->fotos()->max('orden');

        foreach ($request->file('fotos') as $archivo) {
            $ruta = $archivo->store("vehiculos/{$vehiculo->id}", 'public');

            FotoVehiculo::create([
                'vehiculo_id'  => $vehiculo->id,
                'ruta'         => $ruta,
                'orden'        => ++$orden,
                'es_principal' => ! $vehiculo->fotos()->where('es_principal', true)->exists(),
            ]);
        }
    }
}
