<?php

namespace App\Http\Controllers;

use App\Enums\EstadoVehiculo;
use App\Models\Alerta;
use App\Models\Consulta;
use App\Models\Cuota;
use App\Models\Tasacion;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Services\FinanciamientoService;

class DashboardController extends Controller
{
    public function __invoke(FinanciamientoService $financiamiento)
    {
        $inicioMes = now()->startOfMonth()->toDateString();

        $ventasMes = Venta::vigentes()
            ->whereDate('fecha', '>=', $inicioMes)
            ->with('vehiculo')
            ->get();

        return view('dashboard', [
            'disponibles'      => Vehiculo::disponibles()->count(),
            'vendidosMes'      => $ventasMes->count(),
            'montoMes'         => $ventasMes->sum(fn (Venta $v) => $v->monto_en_guaranies),
            'cuotasVencidas'   => Cuota::vencidas()->count(),
            'cuotasPorVencer'  => Cuota::porVencer(config('gestautos.alertas.dias_previo_aviso'))->count(),
            'consultasNuevas'  => Consulta::nuevas()->count(),
            'tasacionesNuevas' => Tasacion::where('estado', 'PENDIENTE')->count(),
            'stockPorEstado'   => Vehiculo::where('activo', true)
                                    ->selectRaw('estado, count(*) as total')
                                    ->groupBy('estado')
                                    ->pluck('total', 'estado'),
            'ultimasVentas'    => Venta::with(['cliente', 'vehiculo'])
                                    ->vigentes()
                                    ->latest('fecha')
                                    ->limit(5)
                                    ->get(),
            'proximasCuotas'   => Cuota::with('financiamiento.venta.cliente', 'financiamiento.venta.vehiculo')
                                    ->abiertas()
                                    ->orderBy('fecha_vencimiento')
                                    ->limit(8)
                                    ->get()
                                    ->each(fn (Cuota $cuota) => $cuota->setAttribute(
                                        'mora_hoy',
                                        $financiamiento->calcularMora($cuota),
                                    )),
            'alertas'          => Alerta::noLeidas()->latest()->limit(5)->get(),
            'estadoDisponible' => EstadoVehiculo::DISPONIBLE,
        ]);
    }
}
