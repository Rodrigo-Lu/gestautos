<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AsegurarRol
{
    /**
     * Uso en rutas: ->middleware('rol:PROPIETARIO,ADMINISTRADOR')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = $request->user();

        if (! $usuario || ! $usuario->activo) {
            abort(403, 'Tu cuenta esta inactiva. Consulta con el administrador.');
        }

        if ($roles !== [] && ! in_array($usuario->rol->value, $roles, true)) {
            abort(403, 'No tenes permiso para acceder a esta seccion.');
        }

        return $next($request);
    }
}
