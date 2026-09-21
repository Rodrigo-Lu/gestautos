<?php

namespace App\Policies;

use App\Enums\TipoReporte;
use App\Models\User;

class ReportePolicy
{
    public function viewAny(User $u): bool
    {
        return $u->esInterno();
    }

    /** Rentabilidad expone precios de compra: solo direccion. */
    public function verTipo(User $u, TipoReporte $tipo): bool
    {
        return $tipo->esSensible() ? $u->esAdministrativo() : $u->esInterno();
    }
}
