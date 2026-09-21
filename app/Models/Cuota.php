<?php

namespace App\Models;

use App\Enums\EstadoCuota;
use App\Enums\TipoPago;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Cuota extends Model
{
    protected $table = 'cuotas';

    protected $fillable = [
        'financiamiento_id', 'numero_cuota', 'monto_cuota', 'monto_capital',
        'monto_interes', 'monto_pagado', 'monto_mora', 'fecha_vencimiento',
        'fecha_pago', 'forma_pago', 'estado', 'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'numero_cuota'      => 'integer',
            'monto_cuota'       => 'decimal:2',
            'monto_capital'     => 'decimal:2',
            'monto_interes'     => 'decimal:2',
            'monto_pagado'      => 'decimal:2',
            'monto_mora'        => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'fecha_pago'        => 'date',
            'forma_pago'        => TipoPago::class,
            'estado'            => EstadoCuota::class,
        ];
    }

    public function financiamiento(): BelongsTo
    {
        return $this->belongsTo(Financiamiento::class, 'financiamiento_id');
    }

    public function cobrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function alertas(): HasMany
    {
        return $this->hasMany(Alerta::class, 'cuota_id');
    }

    public function comprobantes(): HasMany
    {
        return $this->hasMany(Comprobante::class, 'cuota_id');
    }

    public function scopeAbiertas(Builder $q): Builder
    {
        return $q->whereIn('estado', [
            EstadoCuota::PENDIENTE->value,
            EstadoCuota::PARCIAL->value,
            EstadoCuota::VENCIDA->value,
        ]);
    }

    public function scopeVencidas(Builder $q): Builder
    {
        return $q->abiertas()->whereDate('fecha_vencimiento', '<', now()->toDateString());
    }

    public function scopePorVencer(Builder $q, int $dias = 5): Builder
    {
        return $q->abiertas()->whereBetween('fecha_vencimiento', [
            now()->toDateString(),
            now()->addDays($dias)->toDateString(),
        ]);
    }

    public function scopeDelCliente(Builder $q, int $clienteId): Builder
    {
        return $q->whereHas('financiamiento.venta', fn (Builder $v) => $v->where('cliente_id', $clienteId));
    }

    public function getSaldoAttribute(): float
    {
        return round((float) $this->monto_cuota - (float) $this->monto_pagado, 2);
    }

    public function getDiasAtrasoAttribute(): int
    {
        if ($this->estaPagada() || $this->fecha_vencimiento->isFuture()) {
            return 0;
        }

        return (int) $this->fecha_vencimiento->diffInDays(Carbon::today());
    }

    public function estaPagada(): bool
    {
        return $this->estado === EstadoCuota::PAGADA;
    }

    public function getTotalAPagarAttribute(): float
    {
        return round($this->saldo + (float) $this->monto_mora, 2);
    }
}
