<?php

namespace App\Http\Controllers;

use App\Enums\EstadoConsulta;
use App\Models\Consulta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class ConsultaController extends Controller
{
    public function index(Request $request)
    {
        $consultas = Consulta::with(['vehiculo', 'cliente', 'responsable'])
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->orderByDesc('fecha')
            ->paginate(20)
            ->withQueryString();

        return view('consultas.index', [
            'consultas' => $consultas,
            'estado'    => $request->input('estado'),
            'estados'   => EstadoConsulta::opciones(),
        ]);
    }

    public function actualizar(Request $request, Consulta $consulta)
    {
        $datos = $request->validate([
            'estado' => ['required', new Enum(EstadoConsulta::class)],
        ]);

        $consulta->update([
            'estado'         => $datos['estado'],
            'usuario_id'     => $request->user()->id,
            'fecha_contacto' => $datos['estado'] === EstadoConsulta::NUEVA->value
                ? null
                : ($consulta->fecha_contacto ?? now()->toDateString()),
        ]);

        return back()->with('exito', 'Consulta actualizada.');
    }
}
