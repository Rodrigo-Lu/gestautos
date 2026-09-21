<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use Illuminate\Support\Facades\Storage;

class ComprobanteController extends Controller
{
    public function descargar(Comprobante $comprobante)
    {
        abort_unless(auth()->user()->esInterno(), 403);
        abort_unless($comprobante->ruta_archivo && Storage::disk('public')->exists($comprobante->ruta_archivo), 404);

        return Storage::disk('public')->download(
            $comprobante->ruta_archivo,
            $comprobante->numero.'.pdf'
        );
    }
}
