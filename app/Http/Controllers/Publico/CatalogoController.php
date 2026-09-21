<?php

namespace App\Http\Controllers\Publico;

use App\Enums\TipoCombustible;
use App\Enums\TipoTransmision;
use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

/**
 * Catalogo publico. No requiere cuenta: el visitante entra y mira.
 */
class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $filtros = $request->only([
            'q', 'marca', 'anio_desde', 'anio_hasta',
            'precio_desde', 'precio_hasta', 'combustible', 'transmision',
        ]);

        $vehiculos = Vehiculo::with('fotoPrincipal')
            ->disponibles()
            ->filtrar($filtros)
            ->orderByDesc('fecha_ingreso')
            ->paginate(config('gestautos.catalogo.por_pagina'))
            ->withQueryString();

        return view('publico.catalogo', [
            'vehiculos'    => $vehiculos,
            'filtros'      => $filtros,
            'marcas'       => Vehiculo::disponibles()->distinct()->orderBy('marca')->pluck('marca'),
            'combustibles' => TipoCombustible::opciones(),
            'transmisiones'=> TipoTransmision::opciones(),
        ]);
    }

    public function show(Vehiculo $vehiculo)
    {
        abort_unless($vehiculo->activo, 404);

        $vehiculo->load('fotos');

        return view('publico.vehiculo', [
            'vehiculo'    => $vehiculo,
            'plazos'      => config('gestautos.financiamiento.plazos'),
            'tasa'        => config('gestautos.financiamiento.tasa_mensual_default'),
            'similares'   => Vehiculo::disponibles()
                                ->where('id', '!=', $vehiculo->id)
                                ->where('marca', $vehiculo->marca)
                                ->with('fotoPrincipal')
                                ->limit(3)
                                ->get(),
        ]);
    }
}
