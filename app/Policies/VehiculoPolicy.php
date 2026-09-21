<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vehiculo;

class VehiculoPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->esInterno();
    }

    public function view(User $u, Vehiculo $v): bool
    {
        return $u->esInterno();
    }

    public function create(User $u): bool
    {
        return $u->esInterno();
    }

    public function update(User $u, Vehiculo $v): bool
    {
        return $u->esInterno();
    }

    /** Dar de baja un vehiculo es decision de la direccion. */
    public function delete(User $u, Vehiculo $v): bool
    {
        return $u->esAdministrativo();
    }

    /** El precio de compra es informacion reservada. */
    public function verCosto(User $u): bool
    {
        return $u->esAdministrativo();
    }
}
