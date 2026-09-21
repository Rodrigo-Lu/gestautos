<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Services\FinanciamientoService;
use Illuminate\Http\Request;

/**
 * Simulador publico de cuotas. Usa el mismo servicio que la venta real,
 * asi el numero que ve el visitante es el que despues se le cobra.
 */
class SimuladorController extends Controller
{
    public function __invoke(Request $request, FinanciamientoService $financiamiento)
    {
        $datos = $request->validate([
            'vehiculo_id'     => ['nullable', 'exists:vehiculos,id'],
            'precio'          => ['required_without:vehiculo_id', 'nullable', 'numeric', 'min:1'],
            'anticipo'        => ['nullable', 'numeric', 'min:0'],
            'cantidad_cuotas' => ['required', 'integer', 'min:1', 'max:72'],
        ]);

        $vehiculo = isset($datos['vehiculo_id']) ? Vehiculo::find($datos['vehiculo_id']) : null;
        $precio   = (float) ($vehiculo?->precio ?? $datos['precio']);
        $moneda   = $vehiculo?->moneda ?? \App\Enums\Moneda::GS;
        $anticipo = (float) ($datos['anticipo'] ?? 0);

        if ($anticipo >= $precio) {
            return back()
                ->withInput()
                ->withErrors(['anticipo' => 'El anticipo no puede cubrir el precio completo.']);
        }

        $resultado = $financiamiento->simular(
            precio: $precio,
            anticipo: $anticipo,
            cantidadCuotas: (int) $datos['cantidad_cuotas'],
            tasaMensual: (float) config('gestautos.financiamiento.tasa_mensual_default'),
            decimales: $moneda->decimales(),
        );

        $comparativo = $financiamiento->comparar(
            $precio,
            $anticipo,
            config('gestautos.financiamiento.plazos'),
            (float) config('gestautos.financiamiento.tasa_mensual_default'),
            $moneda->decimales(),
        );

        return view('publico.simulador', [
            'vehiculo'    => $vehiculo,
            'precio'      => $precio,
            'moneda'      => $moneda,
            'anticipo'    => $anticipo,
            'cuotas'      => (int) $datos['cantidad_cuotas'],
            'resultado'   => $resultado,
            'comparativo' => $comparativo,
            'tasa'        => config('gestautos.financiamiento.tasa_mensual_default'),
        ]);
    }
}
