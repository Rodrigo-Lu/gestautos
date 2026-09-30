<?php

namespace App\Console\Commands;

use App\Enums\EstadoCuota;
use App\Enums\TipoAlerta;
use App\Models\Alerta;
use App\Models\Cuota;
use App\Services\FinanciamientoService;
use Illuminate\Console\Command;

/**
 * Genera solamente los avisos automáticos de mañana y de hoy.
 */
class GenerarAlertasCuotas extends Command
{
    protected $signature = 'alertas:cuotas';

    protected $description = 'Marca cuotas vencidas y genera avisos de hoy y mañana';

    public function handle(FinanciamientoService $financiamiento): int
    {
        $vencidas = $this->marcarVencidas($financiamiento);
        $venceHoy = $this->generarAvisos(TipoAlerta::VENCE_HOY, now()->toDateString());
        $porVencer = $this->generarAvisos(TipoAlerta::POR_VENCER, now()->addDay()->toDateString());

        $this->info("Cuotas marcadas como vencidas: {$vencidas}");
        $this->info("Avisos de hoy generados: {$venceHoy}");
        $this->info("Avisos de mañana generados: {$porVencer}");

        return self::SUCCESS;
    }

    private function marcarVencidas(FinanciamientoService $financiamiento): int
    {
        $procesadas = 0;

        Cuota::with('financiamiento')
            ->whereIn('estado', [
                EstadoCuota::PENDIENTE->value,
                EstadoCuota::PARCIAL->value,
            ])
            ->whereDate('fecha_vencimiento', '<', now()->toDateString())
            ->chunkById(200, function ($cuotas) use ($financiamiento, &$procesadas) {
                foreach ($cuotas as $cuota) {
                    $cuota->update(['estado' => EstadoCuota::VENCIDA]);
                    $cuota->update(['monto_mora' => $financiamiento->calcularMora($cuota)]);
                    $procesadas++;
                }
            });

        return $procesadas;
    }

    private function generarAvisos(TipoAlerta $tipo, string $fecha): int
    {
        $generadas = 0;

        Cuota::with('financiamiento.venta.cliente')
            ->whereIn('estado', [
                EstadoCuota::PENDIENTE->value,
                EstadoCuota::PARCIAL->value,
            ])
            ->whereDate('fecha_vencimiento', $fecha)
            ->chunkById(200, function ($cuotas) use ($tipo, &$generadas) {
                foreach ($cuotas as $cuota) {
                    $cliente = $cuota->financiamiento->venta->cliente;

                    Alerta::firstOrCreate(
                        ['cuota_id' => $cuota->id, 'tipo' => $tipo->value],
                        [
                            'mensaje' => sprintf(
                                '%s: cuota %d de %s vence el %s.',
                                $tipo->etiqueta(),
                                $cuota->numero_cuota,
                                $cliente->nombre,
                                $cuota->fecha_vencimiento->format('d/m/Y')
                            ),
                            'fecha_generacion' => now()->toDateString(),
                            'leida'            => false,
                        ]
                    );

                    $generadas++;
                }
            });

        return $generadas;
    }
}
