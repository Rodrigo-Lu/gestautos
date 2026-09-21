<?php

namespace Database\Seeders;

use App\Enums\EstadoCuota;
use App\Enums\EstadoVehiculo;
use App\Enums\EstadoVenta;
use App\Enums\Rol;
use App\Enums\TipoPago;
use App\Enums\TipoVenta;
use App\Models\Cliente;
use App\Models\Financiamiento;
use App\Models\Tasacion;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Venta;
use App\Services\FinanciamientoService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Datos de prueba con el volumen real de JP Automotores:
 * 25 a 40 vehiculos en stock y 10 a 15 ventas por mes.
 */
class DemoSeeder extends Seeder
{
    public function run(FinanciamientoService $financiamiento): void
    {
        $vendedores = User::whereIn('rol', [Rol::EMPLEADO->value, Rol::ADMINISTRADOR->value])
            ->where('activo', true)
            ->get();

        $clientes  = Cliente::factory()->count(25)->create();
        $vehiculos = Vehiculo::factory()->count(35)->create();
        Tasacion::factory()->count(8)->create();

        $tasaMensual = (float) config('gestautos.financiamiento.tasa_mensual_default');
        $tasaMora    = (float) config('gestautos.financiamiento.tasa_mora_diaria');

        // 18 ventas repartidas en los ultimos 6 meses.
        $paraVender = $vehiculos->random(18);

        foreach ($paraVender as $i => $vehiculo) {
            $cliente   = $clientes->random();
            $vendedor  = $vendedores->random();
            $fecha     = Carbon::today()->subDays(random_int(5, 180));
            $financiada = $i % 5 !== 0; // cuatro de cada cinco son financiadas

            $venta = Venta::create([
                'vehiculo_id'         => $vehiculo->id,
                'cliente_id'          => $cliente->id,
                'usuario_id'          => $vendedor->id,
                'fecha'               => $fecha->toDateString(),
                'precio_venta'        => $vehiculo->precio,
                'monto_permuta'       => 0,
                'moneda'              => $vehiculo->moneda,
                'tipo_venta'          => $financiada ? TipoVenta::FINANCIADO : TipoVenta::CONTADO,
                'forma_pago_anticipo' => TipoPago::EFECTIVO,
                'estado'              => $financiada ? EstadoVenta::VIGENTE : EstadoVenta::FINALIZADA,
            ]);

            $vehiculo->update(['estado' => EstadoVehiculo::VENDIDO]);

            if (! $financiada) {
                continue;
            }

            $anticipo   = round((float) $vehiculo->precio * 0.25, 0);
            $aFinanciar = (float) $vehiculo->precio - $anticipo;
            $cuotas     = collect([12, 18, 24, 36])->random();
            $primerVto  = $fecha->copy()->addMonthNoOverflow();

            $plan = $financiamiento->generarPlan($aFinanciar, $cuotas, $tasaMensual, $primerVto, 0);

            $fin = Financiamiento::create([
                'venta_id'                 => $venta->id,
                'monto_anticipo'           => $anticipo,
                'monto_financiado'         => $aFinanciar,
                'monto_total_con_interes'  => $plan['total'],
                'cantidad_cuotas'          => $cuotas,
                'tasa_interes_mensual'     => $tasaMensual,
                'tasa_mora_diaria'         => $tasaMora,
                'fecha_primer_vencimiento' => $primerVto->toDateString(),
            ]);

            foreach ($plan['cuotas'] as $c) {
                $vencida = $c['fecha_vencimiento']->isPast();
                // Deja algunas cuotas vencidas sin pagar: material para las alertas.
                $pagada  = $vencida && random_int(1, 10) <= 8;

                $fin->cuotas()->create([
                    'numero_cuota'      => $c['numero_cuota'],
                    'monto_cuota'       => $c['monto_cuota'],
                    'monto_capital'     => $c['monto_capital'],
                    'monto_interes'     => $c['monto_interes'],
                    'monto_pagado'      => $pagada ? $c['monto_cuota'] : 0,
                    'fecha_vencimiento' => $c['fecha_vencimiento']->toDateString(),
                    'fecha_pago'        => $pagada ? $c['fecha_vencimiento']->toDateString() : null,
                    'forma_pago'        => $pagada ? TipoPago::EFECTIVO : null,
                    'estado'            => match (true) {
                        $pagada  => EstadoCuota::PAGADA,
                        $vencida => EstadoCuota::VENCIDA,
                        default  => EstadoCuota::PENDIENTE,
                    },
                    'usuario_id'        => $pagada ? $vendedor->id : null,
                ]);
            }
        }

        // Un cliente con acceso al portal, para probar "mis cuotas".
        $conAcceso = Venta::whereHas('financiamiento')->first()?->cliente;

        if ($conAcceso) {
            $usuario = User::create([
                'nombre'   => $conAcceso->nombre,
                'email'    => 'cliente@ejemplo.com',
                'password' => 'gestautos2026',
                'rol'      => Rol::CLIENTE,
                'activo'   => true,
            ]);

            $conAcceso->update(['usuario_id' => $usuario->id]);
        }
    }
}
