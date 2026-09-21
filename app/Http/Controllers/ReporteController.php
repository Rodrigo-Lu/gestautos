<?php

namespace App\Http\Controllers;

use App\Enums\TipoReporte;
use App\Models\Reporte;
use App\Services\ReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ReporteController extends Controller
{
    public function __construct(private ReporteService $reportes) {}

    public function index()
    {
        $this->authorize('viewAny', Reporte::class);

        return view('reportes.index', [
            'tipos'      => TipoReporte::cases(),
            'historial'  => Reporte::with('usuario')->latest()->limit(10)->get(),
        ]);
    }

    public function generar(Request $request, string $tipo)
    {
        $tipoReporte = TipoReporte::tryFrom(strtoupper($tipo)) ?? abort(404);

        abort_unless(Gate::allows('verTipo', [Reporte::class, $tipoReporte]), 403);

        $datos = $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date', 'after_or_equal:desde'],
        ]);

        $desde = $datos['desde'] ?? now()->startOfMonth()->toDateString();
        $hasta = $datos['hasta'] ?? now()->toDateString();

        $resultado = match ($tipoReporte) {
            TipoReporte::VENTAS       => $this->reportes->ventas($desde, $hasta),
            TipoReporte::INVENTARIO   => $this->reportes->inventario(),
            TipoReporte::COBRANZAS    => $this->reportes->cobranzas($desde, $hasta),
            TipoReporte::RENTABILIDAD => $this->reportes->rentabilidad($desde, $hasta),
        };

        $vista = 'reportes.'.strtolower($tipoReporte->value);

        if ($request->boolean('pdf')) {
            return $this->descargarPdf($request, $tipoReporte, $vista, $resultado, $desde, $hasta);
        }

        return view($vista, $resultado + [
            'tipo'  => $tipoReporte,
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    private function descargarPdf(Request $request, TipoReporte $tipo, string $vista, array $resultado, string $desde, string $hasta)
    {
        $pdf = Pdf::loadView($vista.'-pdf', $resultado + [
            'tipo'  => $tipo,
            'desde' => $desde,
            'hasta' => $hasta,
        ])->setPaper('a4', 'landscape');

        $nombre = strtolower($tipo->value).'-'.now()->format('Ymd-His').'.pdf';
        $ruta   = 'reportes/'.$nombre;

        Storage::disk('public')->put($ruta, $pdf->output());

        Reporte::create([
            'tipo'             => $tipo,
            'fecha_generacion' => now()->toDateString(),
            'fecha_desde'      => $desde,
            'fecha_hasta'      => $hasta,
            'ruta_archivo'     => $ruta,
            'parametros'       => $request->only(['desde', 'hasta']),
            'usuario_id'       => $request->user()->id,
        ]);

        return $pdf->download($nombre);
    }
}
