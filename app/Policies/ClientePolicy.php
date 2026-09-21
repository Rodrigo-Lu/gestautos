<?php

namespace App\Policies;

use App\Models\Cliente;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $u): bool
    {
        return $u->esInterno();
    }

    public function view(User $u, Cliente $c): bool
    {
        return $u->esInterno();
    }

    public function create(User $u): bool
    {
        return $u->esInterno();
    }

    public function update(User $u, Cliente $c): bool
    {
        return $u->esInterno();
    }

    public function delete(User $u, Cliente $c): bool
    {
        return $u->esAdministrativo();
    }

    /** Crear o revocar el acceso al portal del cliente. */
    public function gestionarAcceso(User $u, Cliente $c): bool
    {
        return $u->esAdministrativo();
    }
}
