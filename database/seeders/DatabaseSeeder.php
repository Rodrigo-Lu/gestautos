<?php

namespace Database\Seeders;

use App\Enums\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            ['Juan Perez',       'propietario@jpautomotores.com.py', Rol::PROPIETARIO,   true],
            ['Administracion',   'admin@jpautomotores.com.py',       Rol::ADMINISTRADOR, true],
            ['Vendedor Uno',     'vendedor1@jpautomotores.com.py',   Rol::EMPLEADO,      true],
            ['Vendedor Dos',     'vendedor2@jpautomotores.com.py',   Rol::EMPLEADO,      true],
            ['Vendedor De Baja', 'inactivo@jpautomotores.com.py',    Rol::EMPLEADO,      false],
        ];

        foreach ($usuarios as [$nombre, $email, $rol, $activo]) {
            User::updateOrCreate(
                ['email' => $email],
                [
                    'nombre'   => $nombre,
                    'password' => 'gestautos2026',
                    'rol'      => $rol,
                    'activo'   => $activo,
                ]
            );
        }

        if (app()->environment('local', 'testing')) {
            $this->call(DemoSeeder::class);
        }
    }
}
