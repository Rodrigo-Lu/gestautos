<?php

namespace App\Console\Commands;

use App\Enums\EstadoCuota;
use App\Enums\TipoAlerta;
use App\Models\Alerta;
use App\Models\Cuota;
use App\Services\FinanciamientoService;
use Illuminate\Console\Command;

/**
 * Comando diario: sincroniza el estado de las cuotas con la fecha real,
 * recalcula la mora y genera las alertas del personal.
 *
 * Es el corazon del requisito "alertas automaticas de cuotas vencidas".
 */
class GenerarAlertasCuotas extends Command
{
    protected $signature = 'gestautos:alertas
                            {--dias= : Dias de anticipacion para el aviso previo}';

    protected $description = 'Marca cuotas vencidas, recalcula la mora y genera alertas';

    public function handle(FinanciamientoService $financiamiento): int
    {
        $dias = (int) ($this->option('dias') ?? config('gestautos.alertas.dias_previo_aviso'));

        $vencidas = $this->procesarVencidas($financiamiento);
        $porVencer = $this->procesarPorVencer($dias);

        $this->info("Cuotas vencidas procesadas: {$vencidas}");
        $this->info("Avisos previos generados: {$porVencer}");

        return self::SUCCESS;
    }

    private function procesarVencidas(FinanciamientoService $financiamiento): int
    {
        $procesadas = 0;

        Cuota::with('financiamiento.venta.cliente')
            ->vencidas()
            ->chunkById(200, function ($cuotas) use ($financiamiento, &$procesadas) {
                foreach ($cuotas as $cuota) {
                    $cuota->update([
                        'estado'     => EstadoCuota::VENCIDA,
                        'monto_mora' => $financiamiento->calcularMora($cuota),
                    ]);

                    $cliente = $cuota->financiamiento->venta->cliente;

                    Alerta::updateOrCreate(
                        ['cuota_id' => $cuota->id, 'tipo' => TipoAlerta::VENCIDA->value],
                        [
                            'mensaje' => sprintf(
                                'Cuota %d de %s vencida hace %d dia(s).',
                                $cuota->numero_cuota,
                                $cliente->nombre,
                                $cuota->dias_atraso
                            ),
                            'fecha_generacion' => now()->toDateString(),
                            'leida'            => false,
                        ]
                    );

                    $procesadas++;
                }
            });

        return $procesadas;
    }

    private function procesarPorVencer(int $dias): int
    {
        $generadas = 0;

        Cuota::with('financiamiento.venta.cliente')
            ->porVencer($dias)
            ->chunkById(200, function ($cuotas) use (&$generadas) {
                foreach ($cuotas as $cuota) {
                    $cliente = $cuota->financiamiento->venta->cliente;

                    Alerta::updateOrCreate(
                        ['cuota_id' => $cuota->id, 'tipo' => TipoAlerta::POR_VENCER->value],
                        [
                            'mensaje' => sprintf(
                                'Cuota %d de %s vence el %s.',
                                $cuota->numero_cuota,
                                $cliente->nombre,
                                $cuota->fecha_vencimiento->format('d/m/Y')
                            ),
                            'fecha_generacion' => now()->toDateString(),
                        ]
                    );

                    $generadas++;
                }
            });

        return $generadas;
    }
}
