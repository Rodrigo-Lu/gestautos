<?php

namespace App\Observers;

use App\Models\Auditoria;
use Illuminate\Database\Eloquent\Model;

/**
 * Deja rastro de cada alta, cambio y baja en los modelos sensibles.
 * Responde directamente al problema de "perdida de datos" relevado
 * en JP Automotores: nada desaparece sin dejar quien y cuando.
 */
class AuditoriaObserver
{
    public function created(Model $modelo): void
    {
        $this->registrar($modelo, 'CREADO', null, $modelo->getAttributes());
    }

    public function updated(Model $modelo): void
    {
        $cambios = $modelo->getChanges();
        unset($cambios['updated_at']);

        if ($cambios === []) {
            return;
        }

        $antes = collect($modelo->getOriginal())
            ->only(array_keys($cambios))
            ->all();

        $this->registrar($modelo, 'MODIFICADO', $antes, $cambios);
    }

    public function deleted(Model $modelo): void
    {
        $this->registrar($modelo, 'ELIMINADO', $modelo->getOriginal(), null);
    }

    public function restored(Model $modelo): void
    {
        $this->registrar($modelo, 'RESTAURADO', null, $modelo->getAttributes());
    }

    private function registrar(Model $modelo, string $accion, ?array $antes, ?array $despues): void
    {
        Auditoria::create([
            'usuario_id'    => auth()->id(),
            'modelo'        => class_basename($modelo),
            'modelo_id'     => $modelo->getKey(),
            'accion'        => $accion,
            'datos_antes'   => $this->limpiar($antes),
            'datos_despues' => $this->limpiar($despues),
            'ip'            => request()->ip(),
            'created_at'    => now(),
        ]);
    }

    private function limpiar(?array $datos): ?array
    {
        if ($datos === null) {
            return null;
        }

        return collect($datos)->except(['password', 'remember_token'])->all();
    }
}
