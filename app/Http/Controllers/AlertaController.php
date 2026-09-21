<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use Illuminate\Http\Request;

class AlertaController extends Controller
{
    public function index(Request $request)
    {
        $alertas = Alerta::with('cuota.financiamiento.venta.cliente')
            ->when($request->input('vista') !== 'todas', fn ($q) => $q->noLeidas())
            ->latest('fecha_generacion')
            ->paginate(20)
            ->withQueryString();

        return view('alertas.index', [
            'alertas' => $alertas,
            'vista'   => $request->input('vista', 'pendientes'),
        ]);
    }

    public function marcarLeida(Alerta $alerta)
    {
        $alerta->update(['leida' => true]);

        return back()->with('exito', 'Alerta archivada.');
    }

    public function marcarTodas()
    {
        Alerta::noLeidas()->update(['leida' => true]);

        return back()->with('exito', 'Todas las alertas quedaron archivadas.');
    }
}
