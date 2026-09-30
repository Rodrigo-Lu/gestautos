<?php

namespace App\Http\Controllers\Publico;

use App\Enums\EstadoConsulta;
use App\Enums\EstadoVehiculo;
use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Vehiculo;

class WhatsAppController extends Controller
{
    public function redirect(Vehiculo $vehiculo)
    {
        abort_unless(
            $vehiculo->activo && $vehiculo->estado === EstadoVehiculo::DISPONIBLE,
            404
        );

        $enlace = $vehiculo->whatsappLink();
        abort_unless($enlace, 503, 'WhatsApp no está configurado.');

        Consulta::create([
            'nombre'      => 'Visitante de WhatsApp',
            'telefono'    => 'WHATSAPP',
            'mensaje'     => "Consulta iniciada por WhatsApp para {$vehiculo->descripcion_corta}.",
            'fecha'       => now()->toDateString(),
            'estado'      => EstadoConsulta::NUEVA,
            'origen'      => 'WHATSAPP',
            'vehiculo_id' => $vehiculo->id,
        ]);

        return redirect()->away($enlace);
    }
}
