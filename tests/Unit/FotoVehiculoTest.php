<?php

namespace Tests\Unit;

use App\Models\FotoVehiculo;
use Illuminate\Http\Request;
use Tests\TestCase;

class FotoVehiculoTest extends TestCase
{
    public function test_la_url_de_la_foto_usa_el_host_y_puerto_de_la_peticion(): void
    {
        $this->app->instance(
            'request',
            Request::create('http://gestautos.test:8080/vehiculos/1', 'GET')
        );

        $foto = new FotoVehiculo(['ruta' => 'vehiculos/1/foto.webp']);

        $this->assertSame(
            'http://gestautos.test:8080/storage/vehiculos/1/foto.webp',
            $foto->url
        );
    }
}
