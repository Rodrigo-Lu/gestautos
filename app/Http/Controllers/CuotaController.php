<?php

namespace App\Http\Controllers;

use App\Enums\EstadoCuota;
use App\Enums\TipoPago;
use App\Http\Requests\CobroRequest;
use App\Models\Cuota;
use App\Services\CobranzaService;
use App\Services\FinanciamientoService;
use Illuminate\Http\Request;

class CuotaController extends Controller
{
    public function index(Request $request, FinanciamientoService $financiamiento)
    {
        $this->authorize('viewAny', \App\Models\Venta::class);

        $cuotas = Cuota::with('financiamiento.venta.cliente', 'financiamiento.venta.vehiculo')
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->when($request->input('vista') === 'vencidas', fn ($q) => $q->vencidas())
            ->when($request->input('vista') === 'por_vencer', fn ($q) => $q->porVencer())
            ->when($request->filled('q'), function ($q) use ($request) {
                $t = '%'.$request->input('q').'%';
                $q->whereHas('financiamiento.venta.cliente', function ($c) use ($t) {
                    $c->where('nombre', 'like', $t)->orWhere('cedula', 'like', $t);
                });
            })
            ->orderBy('fecha_vencimiento')
            ->paginate(20)
            ->withQueryString();

        // Mora al dia de hoy, sin tocar la base: se congela recien al cobrar.
        $cuotas->each(fn (Cuota $c) => $c->setAttribute('mora_hoy', $financiamiento->calcularMora($c)));

        return view('cuotas.index', [
            'cuotas'  => $cuotas,
            'filtros' => $request->only(['estado', 'vista', 'q']),
            'estados' => EstadoCuota::opciones(),
        ]);
    }

    public function cobrar(Cuota $cuota, FinanciamientoService $financiamiento)
    {
        $cuota->load('financiamiento.venta.cliente', 'financiamiento.venta.vehiculo');
        $this->authorize('cobrar', $cuota->financiamiento->venta);

        return view('cuotas.cobrar', [
            'cuota'      => $cuota,
            'mora'       => $financiamiento->calcularMora($cuota),
            'formasPago' => TipoPago::opciones(),
        ]);
    }

    public function registrarPago(CobroRequest $request, Cuota $cuota, CobranzaService $cobranza)
    {
        $cuota->load('financiamiento.venta');
        $this->authorize('cobrar', $cuota->financiamiento->venta);

        $cobranza->registrarPago(
            $cuota,
            (float) $request->validated('monto'),
            TipoPago::from($request->validated('forma_pago')),
            $request->user()
        );

        return redirect()
            ->route('ventas.show', $cuota->financiamiento->venta)
            ->with('exito', 'Pago registrado y recibo generado.');
    }
}
