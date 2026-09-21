<?php

namespace App\Http\Controllers;

use App\Models\FotoVehiculo;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FotoVehiculoController extends Controller
{
    public function principal(Vehiculo $vehiculo, FotoVehiculo $foto)
    {
        $this->authorize('update', $vehiculo);
        abort_unless($foto->vehiculo_id === $vehiculo->id, 404);

        DB::transaction(function () use ($vehiculo, $foto) {
            $vehiculo->fotos()->update(['es_principal' => false]);
            $foto->update(['es_principal' => true]);
        });

        return back()->with('exito', 'Foto principal actualizada.');
    }

    public function destroy(Vehiculo $vehiculo, FotoVehiculo $foto)
    {
        $this->authorize('update', $vehiculo);
        abort_unless($foto->vehiculo_id === $vehiculo->id, 404);

        DB::transaction(function () use ($vehiculo, $foto) {
            Storage::disk('public')->delete($foto->ruta);
            $eraPrincipal = $foto->es_principal;
            $foto->delete();

            if ($eraPrincipal) {
                $vehiculo->fotos()->orderBy('orden')->first()?->update(['es_principal' => true]);
            }
        });

        return back()->with('exito', 'Foto eliminada.');
    }
}
