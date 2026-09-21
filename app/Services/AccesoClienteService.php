<?php

namespace App\Services;

use App\Enums\Rol;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Crea la cuenta del portal para un cliente ya registrado.
 * Devuelve la contrasena temporal para entregarla en mano.
 */
class AccesoClienteService
{
    /** @return array{usuario: User, password: string} */
    public function crear(Cliente $cliente): array
    {
        if ($cliente->tieneAcceso()) {
            throw ValidationException::withMessages([
                'email' => 'Ese cliente ya tiene acceso al portal.',
            ]);
        }

        if (blank($cliente->email)) {
            throw ValidationException::withMessages([
                'email' => 'Carga primero el correo del cliente para poder darle acceso.',
            ]);
        }

        return DB::transaction(function () use ($cliente) {
            $password = Str::password(10, symbols: false);

            $usuario = User::create([
                'nombre'   => $cliente->nombre,
                'email'    => $cliente->email,
                'password' => $password,
                'rol'      => Rol::CLIENTE,
                'activo'   => true,
            ]);

            $cliente->update(['usuario_id' => $usuario->id]);

            return ['usuario' => $usuario, 'password' => $password];
        });
    }

    public function revocar(Cliente $cliente): void
    {
        DB::transaction(function () use ($cliente) {
            $usuario = $cliente->usuario;
            $cliente->update(['usuario_id' => null]);
            $usuario?->delete();
        });
    }
}
