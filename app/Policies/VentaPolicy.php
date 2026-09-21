<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Venta;

class VentaPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->esInterno();
    }

    public function view(User $u, Venta $v): bool
    {
        return $u->esInterno();
    }

    public function create(User $u): bool
    {
        return $u->esInterno();
    }

    /** Anular una venta solo la direccion. */
    public function anular(User $u, Venta $v): bool
    {
        return $u->esAdministrativo() && $v->puedeAnularse();
    }

    public function cobrar(User $u, Venta $v): bool
    {
        return $u->esInterno();
    }
}
