<?php

namespace Tests\Unit;

use App\Services\FinanciamientoService;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class FinanciamientoServiceTest extends TestCase
{
    private FinanciamientoService $servicio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->servicio = new FinanciamientoService();
    }

    public function test_genera_la_cantidad_exacta_de_cuotas(): void
    {
        $plan = $this->servicio->generarPlan(120_000_000, 12, 3.5, Carbon::parse('2026-02-10'));

        $this->assertCount(12, $plan['cuotas']);
        $this->assertSame(1, $plan['cuotas'][0]['numero_cuota']);
        $this->assertSame(12, $plan['cuotas'][11]['numero_cuota']);
    }

    public function test_la_suma_de_capitales_iguala_el_monto_financiado(): void
    {
        $monto = 87_500_000;
        $plan  = $this->servicio->generarPlan($monto, 24, 2.9, Carbon::parse('2026-03-05'));

        $sumaCapital = array_sum(array_column($plan['cuotas'], 'monto_capital'));

        $this->assertEqualsWithDelta($monto, $sumaCapital, 1.0);
    }

    public function test_la_suma_de_cuotas_iguala_el_total_informado(): void
    {
        $plan = $this->servicio->generarPlan(60_000_000, 18, 4.0, Carbon::parse('2026-01-15'));

        $sumaCuotas = array_sum(array_column($plan['cuotas'], 'monto_cuota'));

        $this->assertEqualsWithDelta($plan['total'], $sumaCuotas, 1.0);
    }

    public function test_sin_interes_las_cuotas_son_iguales(): void
    {
        $plan = $this->servicio->generarPlan(12_000_000, 12, 0, Carbon::parse('2026-01-10'));

        $montos = array_unique(array_column($plan['cuotas'], 'monto_cuota'));

        $this->assertCount(1, $montos);
        $this->assertSame(1_000_000.0, (float) reset($montos));
    }

    public function test_los_vencimientos_son_mensuales_consecutivos(): void
    {
        $plan = $this->servicio->generarPlan(10_000_000, 3, 0, Carbon::parse('2026-01-31'));

        $this->assertSame('2026-01-31', $plan['cuotas'][0]['fecha_vencimiento']->toDateString());
        $this->assertSame('2026-02-28', $plan['cuotas'][1]['fecha_vencimiento']->toDateString());
        $this->assertSame('2026-03-31', $plan['cuotas'][2]['fecha_vencimiento']->toDateString());
    }

    public function test_el_simulador_devuelve_lo_mismo_que_el_plan_real(): void
    {
        $sim  = $this->servicio->simular(100_000_000, 20_000_000, 12, 3.5);
        $plan = $this->servicio->generarPlan(80_000_000, 12, 3.5, Carbon::today()->addMonthNoOverflow());

        $this->assertSame($plan['valor_cuota'], $sim['valor_cuota']);
        $this->assertSame($plan['total'], $sim['total']);
    }

    public function test_rechaza_monto_cero(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->servicio->generarPlan(0, 12, 3.5, Carbon::today());
    }
}
