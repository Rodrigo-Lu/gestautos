<?php

namespace App\Models;

use App\Enums\TipoAlerta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'tipo', 'mensaje', 'fecha_generacion', 'leida', 'cuota_id', 'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'tipo'             => TipoAlerta::class,
            'leida'            => 'boolean',
            'fecha_generacion' => 'date',
        ];
    }

    public function cuota(): BelongsTo
    {
        return $this->belongsTo(Cuota::class, 'cuota_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeNoLeidas(Builder $q): Builder
    {
        return $q->where('leida', false);
    }
}
