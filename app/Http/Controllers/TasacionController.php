<?php

namespace App\Http\Controllers;

use App\Enums\EstadoTasacion;
use App\Enums\Moneda;
use App\Models\Tasacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TasacionController extends Controller
{
    public function index(Request $request)
    {
        $tasaciones = Tasacion::with('evaluador')
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->input('estado')))
            ->orderByDesc('fecha')
            ->paginate(20)
            ->withQueryString();

        return view('tasaciones.index', [
            'tasaciones' => $tasaciones,
            'estado'     => $request->input('estado'),
            'estados'    => EstadoTasacion::opciones(),
        ]);
    }

    public function evaluar(Request $request, Tasacion $tasacion)
    {
        $datos = $request->validate([
            'estado'         => ['required', new Enum(EstadoTasacion::class)],
            'monto_ofrecido' => ['nullable', 'required_if:estado,EVALUADA', 'numeric', 'min:0'],
            'moneda'         => ['required', new Enum(Moneda::class)],
            'observaciones'  => ['nullable', 'string', 'max:1000'],
        ], [
            'monto_ofrecido.required_if' => 'Para marcarla como evaluada cargá el monto que ofrecemos.',
        ]);

        $tasacion->update($datos + ['usuario_id' => $request->user()->id]);

        return back()->with('exito', 'Tasación actualizada.');
    }
}
