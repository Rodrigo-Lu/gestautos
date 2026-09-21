<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use App\Services\AccesoClienteService;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Cliente::class, 'cliente');
    }

    public function index(Request $request)
    {
        $clientes = Cliente::withCount('ventas')
            ->buscar($request->input('q'))
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('clientes.index', [
            'clientes' => $clientes,
            'q'        => $request->input('q'),
        ]);
    }

    public function create()
    {
        return view('clientes.form', ['cliente' => new Cliente()]);
    }

    public function store(ClienteRequest $request)
    {
        $cliente = Cliente::create($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('exito', 'Cliente registrado.');
    }

    public function show(Cliente $cliente)
    {
        $cliente->load([
            'ventas.vehiculo',
            'ventas.financiamiento.cuotas',
            'consultas.vehiculo',
            'usuario',
        ]);

        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.form', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente)
    {
        $cliente->update($request->validated());

        return redirect()
            ->route('clientes.show', $cliente)
            ->with('exito', 'Cambios guardados.');
    }

    public function destroy(Cliente $cliente)
    {
        if ($cliente->ventas()->exists()) {
            return back()->with('error', 'Ese cliente tiene ventas registradas y no se puede eliminar.');
        }

        $cliente->delete();

        return redirect()->route('clientes.index')->with('exito', 'Cliente dado de baja.');
    }

    public function darAcceso(Cliente $cliente, AccesoClienteService $servicio)
    {
        $this->authorize('gestionarAcceso', $cliente);

        $resultado = $servicio->crear($cliente);

        return back()->with(
            'exito',
            "Acceso creado. Contraseña temporal: {$resultado['password']} (anótala, no se vuelve a mostrar)."
        );
    }

    public function revocarAcceso(Cliente $cliente, AccesoClienteService $servicio)
    {
        $this->authorize('gestionarAcceso', $cliente);

        $servicio->revocar($cliente);

        return back()->with('exito', 'Acceso al portal revocado.');
    }
}
