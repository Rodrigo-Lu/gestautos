<?php

namespace App\Services;

use App\Enums\Moneda;
use App\Enums\TipoComprobante;
use App\Models\Comprobante;
use App\Models\Cuota;
use App\Models\User;
use App\Models\Venta;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Emision de comprobantes con numeracion correlativa y PDF.
 */
class ComprobanteService
{
    public function emitirPorVenta(Venta $venta, User $usuario): Comprobante
    {
        $comprobante = $this->crear(
            tipo: TipoComprobante::COMPROBANTE_VENTA,
            monto: (float) $venta->precio_venta,
            moneda: $venta->moneda,
            usuario: $usuario,
            venta: $venta,
        );

        $venta->load(['cliente', 'vehiculo', 'vendedor', 'financiamiento.cuotas']);

        return $this->generarPdf($comprobante, 'pdf.comprobante-venta', [
            'comprobante' => $comprobante,
            'venta'       => $venta,
        ]);
    }

    public function emitirPorCuota(Cuota $cuota, float $montoCobrado, User $usuario): Comprobante
    {
        $cuota->load('financiamiento.venta.cliente', 'financiamiento.venta.vehiculo');
        $venta = $cuota->financiamiento->venta;

        $comprobante = $this->crear(
            tipo: TipoComprobante::RECIBO_PAGO_CUOTA,
            monto: $montoCobrado,
            moneda: $venta->moneda,
            usuario: $usuario,
            venta: $venta,
            cuota: $cuota,
        );

        return $this->generarPdf($comprobante, 'pdf.recibo-cuota', [
            'comprobante' => $comprobante,
            'cuota'       => $cuota,
            'venta'       => $venta,
            'monto'       => $montoCobrado,
        ]);
    }

    private function crear(
        TipoComprobante $tipo,
        float $monto,
        Moneda $moneda,
        User $usuario,
        ?Venta $venta = null,
        ?Cuota $cuota = null
    ): Comprobante {
        return DB::transaction(function () use ($tipo, $monto, $moneda, $usuario, $venta, $cuota) {
            return Comprobante::create([
                'numero'        => $this->siguienteNumero($tipo),
                'tipo'          => $tipo,
                'fecha_emision' => now()->toDateString(),
                'monto'         => $monto,
                'moneda'        => $moneda,
                'venta_id'      => $venta?->id,
                'cuota_id'      => $cuota?->id,
                'usuario_id'    => $usuario->id,
            ]);
        });
    }

    /**
     * Numeracion correlativa por tipo: CV-0000001, RC-0000001.
     * El lockForUpdate evita que dos ventas simultaneas tomen el mismo numero.
     */
    private function siguienteNumero(TipoComprobante $tipo): string
    {
        $ultimo = Comprobante::where('tipo', $tipo->value)
            ->lockForUpdate()
            ->orderByDesc('numero')
            ->value('numero');

        $secuencia = $ultimo ? ((int) substr($ultimo, 3)) + 1 : 1;

        return $tipo->prefijo().'-'.str_pad((string) $secuencia, 7, '0', STR_PAD_LEFT);
    }

    private function generarPdf(Comprobante $comprobante, string $vista, array $datos): Comprobante
    {
        $pdf  = Pdf::loadView($vista, $datos)->setPaper('a4');
        $ruta = 'comprobantes/'.$comprobante->numero.'.pdf';

        Storage::disk('public')->put($ruta, $pdf->output());

        $comprobante->update(['ruta_archivo' => $ruta]);

        return $comprobante->refresh();
    }
}
