<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $u): bool
    {
        return $u->esAdministrativo();
    }

    public function create(User $u): bool
    {
        return $u->esAdministrativo();
    }

    public function update(User $u, User $objetivo): bool
    {
        return $u->esAdministrativo();
    }

    /** Nadie se desactiva a si mismo. */
    public function desactivar(User $u, User $objetivo): bool
    {
        return $u->esAdministrativo() && $u->id !== $objetivo->id;
    }
}
