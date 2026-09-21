<?php

namespace App\Http\Controllers\Publico;

use App\Enums\EstadoConsulta;
use App\Http\Controllers\Controller;
use App\Http\Requests\ConsultaPublicaRequest;
use App\Models\Cliente;
use App\Models\Consulta;

class ConsultaPublicaController extends Controller
{
    public function store(ConsultaPublicaRequest $request)
    {
        $datos = $request->validated();

        // Si el telefono o el correo ya figuran en un cliente, se vincula sola.
        $cliente = Cliente::where('telefono', $datos['telefono'])
            ->when(filled($datos['email'] ?? null), fn ($q) => $q->orWhere('email', $datos['email']))
            ->first();

        Consulta::create($datos + [
            'fecha'      => now()->toDateString(),
            'estado'     => EstadoConsulta::NUEVA,
            'cliente_id' => $cliente?->id,
        ]);

        return back()->with('exito', 'Recibimos tu consulta. Te contactamos a la brevedad.');
    }
}
