<?php

namespace Tests\Feature;

use App\Enums\Rol;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalClienteTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_cliente_ve_su_pantalla_de_cuotas(): void
    {
        $usuario = User::factory()->rol(Rol::CLIENTE)->create();
        Cliente::factory()->create(['usuario_id' => $usuario->id]);

        $this->actingAs($usuario)->get('/mi-cuenta/cuotas')->assertOk();
    }

    public function test_un_empleado_no_entra_al_portal_del_cliente(): void
    {
        $empleado = User::factory()->rol(Rol::EMPLEADO)->create();

        $this->actingAs($empleado)->get('/mi-cuenta/cuotas')->assertForbidden();
    }

    public function test_un_cliente_no_entra_al_panel_interno(): void
    {
        $usuario = User::factory()->rol(Rol::CLIENTE)->create();

        $this->actingAs($usuario)->get('/panel')->assertForbidden();
    }
}
