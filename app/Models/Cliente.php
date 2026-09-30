<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre', 'cedula', 'telefono', 'direccion', 'email', 'usuario_id',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'cliente_id');
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class, 'cliente_id');
    }

    /** Busqueda por nombre, cedula, telefono o email. */
    public function scopeBuscar(Builder $q, ?string $texto): Builder
    {
        if (blank($texto)) {
            return $q;
        }

        $t = '%'.trim($texto).'%';

        return $q->where(function (Builder $sub) use ($t) {
            $sub->where('nombre', 'like', $t)
                ->orWhere('cedula', 'like', $t)
                ->orWhere('telefono', 'like', $t)
                ->orWhere('email', 'like', $t);
        });
    }

    public function tieneAcceso(): bool
    {
        return $this->usuario_id !== null;
    }

    /**
     * Devuelve el teléfono en formato internacional de Paraguay para wa.me.
     */
    public function whatsappTelefono(): ?string
    {
        $telefono = preg_replace('/\D+/', '', (string) $this->telefono);

        if (blank($telefono)) {
            return null;
        }

        if (str_starts_with($telefono, '00')) {
            $telefono = substr($telefono, 2);
        }

        if (str_starts_with($telefono, '595')) {
            $telefono = '595'.ltrim(substr($telefono, 3), '0');
        } else {
            $telefono = '595'.ltrim($telefono, '0');
        }

        return strlen($telefono) > 3 ? $telefono : null;
    }
}
