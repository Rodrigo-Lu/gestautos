<?php

namespace Tests\Feature;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccesoTest extends TestCase
{
    use RefreshDatabase;

    public function test_el_usuario_inactivo_no_puede_iniciar_sesion(): void
    {
        $usuario = User::factory()->inactivo()->create(['email' => 'baja@jp.com']);

        $respuesta = $this->post('/login', [
            'email'    => 'baja@jp.com',
            'password' => 'gestautos2026',
        ]);

        $respuesta->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_el_empleado_no_entra_a_la_gestion_de_usuarios(): void
    {
        $empleado = User::factory()->rol(Rol::EMPLEADO)->create();

        $this->actingAs($empleado)->get('/panel/usuarios')->assertForbidden();
    }

    public function test_el_administrador_entra_a_la_gestion_de_usuarios(): void
    {
        $admin = User::factory()->rol(Rol::ADMINISTRADOR)->create();

        $this->actingAs($admin)->get('/panel/usuarios')->assertOk();
    }

    public function test_el_catalogo_publico_no_pide_login(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_el_panel_redirige_al_login_si_no_hay_sesion(): void
    {
        $this->get('/panel')->assertRedirect('/login');
    }
}
