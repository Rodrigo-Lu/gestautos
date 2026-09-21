<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use App\Http\Requests\UsuarioRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $usuarios = User::when($request->filled('rol'), fn ($q) => $q->where('rol', $request->input('rol')))
            ->orderBy('nombre')
            ->paginate(20)
            ->withQueryString();

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles'    => Rol::opciones(),
            'rol'      => $request->input('rol'),
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);

        return view('usuarios.form', [
            'usuario' => new User(['activo' => true]),
            'roles'   => Rol::opciones(),
        ]);
    }

    public function store(UsuarioRequest $request)
    {
        User::create($request->validated());

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado.');
    }

    public function edit(User $usuario)
    {
        $this->authorize('update', $usuario);

        return view('usuarios.form', [
            'usuario' => $usuario,
            'roles'   => Rol::opciones(),
        ]);
    }

    public function update(UsuarioRequest $request, User $usuario)
    {
        $this->authorize('update', $usuario);

        $datos = $request->validated();

        if (blank($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        // Nadie puede desactivarse a si mismo y quedar afuera del sistema.
        if ($usuario->id === $request->user()->id) {
            $datos['activo'] = true;
            $datos['rol']    = $usuario->rol->value;
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado.');
    }
}
