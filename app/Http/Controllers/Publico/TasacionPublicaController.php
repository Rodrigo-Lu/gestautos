<?php

namespace App\Http\Controllers\Publico;

use App\Enums\EstadoTasacion;
use App\Http\Controllers\Controller;
use App\Http\Requests\TasacionPublicaRequest;
use App\Models\Tasacion;

/**
 * "Ofrecer vehiculo en permuta" del diagrama de casos de uso publico.
 */
class TasacionPublicaController extends Controller
{
    public function create()
    {
        return view('publico.permuta');
    }

    public function store(TasacionPublicaRequest $request)
    {
        Tasacion::create($request->validated() + [
            'fecha'  => now()->toDateString(),
            'estado' => EstadoTasacion::PENDIENTE,
        ]);

        return redirect()
            ->route('permuta.create')
            ->with('exito', 'Recibimos los datos de tu vehiculo. Te vamos a llamar con una oferta.');
    }
}
