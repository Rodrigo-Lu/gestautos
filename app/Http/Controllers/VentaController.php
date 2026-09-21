<?php

namespace App\Http\Controllers;

use App\Enums\EstadoTasacion;
use App\Enums\EstadoVenta;
use App\Enums\Moneda;
use App\Enums\TipoPago;
use App\Enums\TipoVenta;
use App\Http\Requests\VentaRequest;
use App\Models\Cliente;
use App\Models\Tasacion;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Services\VentaService;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    public function __construct(private VentaService $ventas)
    {
        $this->authorizeResource(Venta::class, 'venta', ['except' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $ventas = Venta::with(['cliente', 'vehiculo', 'vendedor', 'financiamiento'])
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->when($request->filled('tipo'), fn ($q) => $q->where('tipo_venta', $request->input('tipo')))
            ->entre($request->input('desde'), $request->input('hasta'))
            ->orderByDesc('fecha')
            ->paginate(15)
            ->withQueryString();

        return view('ventas.index', [
            'ventas'  => $ventas,
            'filtros' => $request->only(['estado', 'tipo', 'desde', 'hasta']),
            'estados' => EstadoVenta::opciones(),
            'tipos'   => TipoVenta::opciones(),
        ]);
    }

    public function create(Request $request)
    {
        return view('ventas.form', [
            'vehiculos'   => Vehiculo::disponibles()->orderBy('marca')->get(),
            'clientes'    => Cliente::orderBy('nombre')->get(),
            'tasaciones'  => Tasacion::where('estado', EstadoTasacion::PENDIENTE->value)
                                ->orderByDesc('fecha')->get(),
            'monedas'     => Moneda::opciones(),
            'tipos'       => TipoVenta::opciones(),
            'formasPago'  => TipoPago::opciones(),
            'plazos'      => config('gestautos.financiamiento.plazos'),
            'tasaDefault' => config('gestautos.financiamiento.tasa_mensual_default'),
            'moraDefault' => config('gestautos.financiamiento.tasa_mora_diaria'),
            'preseleccion'=> $request->integer('vehiculo_id'),
        ]);
    }

    public function store(VentaRequest $request)
    {
        $venta = $this->ventas->registrar($request->validated(), $request->user());

        return redirect()
            ->route('ventas.show', $venta)
            ->with('exito', 'Venta registrada y comprobante generado.');
    }

    public function show(Venta $venta)
    {
        $venta->load([
            'cliente', 'vehiculo.fotoPrincipal', 'vendedor',
            'financiamiento.cuotas.cobrador', 'comprobantes', 'tasacionPermuta',
        ]);

        return view('ventas.show', compact('venta'));
    }

    public function anular(Request $request, Venta $venta)
    {
        $this->authorize('anular', $venta);

        $datos = $request->validate([
            'motivo_anulacion' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'motivo_anulacion.min' => 'Explicá el motivo de la anulación en al menos 10 caracteres.',
        ]);

        $this->ventas->anular($venta, $datos['motivo_anulacion'], $request->user());

        return back()->with('exito', 'Venta anulada. El vehículo volvió al stock disponible.');
    }
}
