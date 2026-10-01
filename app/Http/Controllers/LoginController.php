<?php

namespace App\Http\Controllers;

use App\Enums\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function mostrar()
    {
        return view('auth.login');
    }

    public function autenticar(Request $request)
    {
        $credenciales = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Ingresá tu correo electrónico.',
            'password.required' => 'Ingresá tu contraseña.',
        ]);

        // La condicion activo entra en la consulta: un usuario dado de baja
        // no puede entrar aunque la contrasena sea correcta.
        $ok = Auth::attempt(
            $credenciales + ['activo' => true],
            $request->boolean('remember')
        );

        if (! $ok) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales no coinciden o el usuario esta inactivo.',
            ]);
        }

        $request->session()->regenerate();

        // El empleado no puede volver a /panel: esa URL es exclusivamente el dashboard.
        // Esto también evita que una URL intended previa fuerce un 403 después del login.
        if (Auth::user()->rol === Rol::EMPLEADO) {
            return redirect()->route('vehiculos.index');
        }

        return redirect()->intended($this->destinoSegunRol());
    }

    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('catalogo.index');
    }

    private function destinoSegunRol(): string
    {
        return match (Auth::user()->rol) {
            Rol::CLIENTE   => route('cliente.cuotas'),
            Rol::EMPLEADO  => route('vehiculos.index'),
            default        => route('dashboard'),
        };
    }
}
