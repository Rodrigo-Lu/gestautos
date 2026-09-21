<?php

namespace Tests\Feature;

use App\Enums\EstadoVehiculo;
use App\Enums\EstadoVenta;
use App\Enums\Moneda;
use App\Enums\Rol;
use App\Enums\TipoVenta;
use App\Models\Cliente;
use App\Models\User;
use App\Models\Vehiculo;
use App\Services\VentaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class VentaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_una_venta_al_contado_marca_el_vehiculo_como_vendido(): void
    {
        [$usuario, $vehiculo, $cliente] = $this->escenario();

        $venta = app(VentaService::class)->registrar([
            'vehiculo_id'  => $vehiculo->id,
            'cliente_id'   => $cliente->id,
            'fecha'        => now()->toDateString(),
            'precio_venta' => 100_000_000,
            'moneda'       => Moneda::GS->value,
            'tipo_venta'   => TipoVenta::CONTADO->value,
        ], $usuario);

        $this->assertSame(EstadoVehiculo::VENDIDO, $vehiculo->refresh()->estado);
        $this->assertSame(EstadoVenta::FINALIZADA, $venta->estado);
        $this->assertNull($venta->financiamiento);
    }

    public function test_una_venta_financiada_genera_todas_las_cuotas(): void
    {
        [$usuario, $vehiculo, $cliente] = $this->escenario();

        $venta = app(VentaService::class)->registrar([
            'vehiculo_id'              => $vehiculo->id,
            'cliente_id'               => $cliente->id,
            'fecha'                    => now()->toDateString(),
            'precio_venta'             => 120_000_000,
            'moneda'                   => Moneda::GS->value,
            'tipo_venta'               => TipoVenta::FINANCIADO->value,
            'monto_anticipo'           => 20_000_000,
            'cantidad_cuotas'          => 12,
            'tasa_interes_mensual'     => 3.5,
            'fecha_primer_vencimiento' => now()->addMonth()->toDateString(),
        ], $usuario);

        $this->assertNotNull($venta->financiamiento);
        $this->assertSame(12, $venta->financiamiento->cuotas()->count());
        $this->assertEqualsWithDelta(
            100_000_000,
            (float) $venta->financiamiento->monto_financiado,
            1.0
        );
    }

    public function test_no_se_puede_vender_dos_veces_el_mismo_vehiculo(): void
    {
        [$usuario, $vehiculo, $cliente] = $this->escenario();

        $datos = [
            'vehiculo_id'  => $vehiculo->id,
            'cliente_id'   => $cliente->id,
            'fecha'        => now()->toDateString(),
            'precio_venta' => 50_000_000,
            'moneda'       => Moneda::GS->value,
            'tipo_venta'   => TipoVenta::CONTADO->value,
        ];

        app(VentaService::class)->registrar($datos, $usuario);

        $this->expectException(ValidationException::class);
        app(VentaService::class)->registrar($datos, $usuario);
    }

    public function test_anular_devuelve_el_vehiculo_al_stock(): void
    {
        [$usuario, $vehiculo, $cliente] = $this->escenario();

        $venta = app(VentaService::class)->registrar([
            'vehiculo_id'  => $vehiculo->id,
            'cliente_id'   => $cliente->id,
            'fecha'        => now()->toDateString(),
            'precio_venta' => 50_000_000,
            'moneda'       => Moneda::GS->value,
            'tipo_venta'   => TipoVenta::CONTADO->value,
        ], $usuario);

        app(VentaService::class)->anular($venta, 'El cliente se arrepintio al dia siguiente.', $usuario);

        $this->assertSame(EstadoVenta::ANULADA, $venta->refresh()->estado);
        $this->assertSame(EstadoVehiculo::DISPONIBLE, $vehiculo->refresh()->estado);
    }

    /** @return array{0: User, 1: Vehiculo, 2: Cliente} */
    private function escenario(): array
    {
        return [
            User::factory()->rol(Rol::ADMINISTRADOR)->create(),
            Vehiculo::factory()->create(),
            Cliente::factory()->create(),
        ];
    }
}
