<?php

namespace App\Models;

use App\Enums\EstadoCuota;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Financiamiento extends Model
{
    protected $table = 'financiamientos';

    protected $fillable = [
        'venta_id', 'monto_anticipo', 'monto_financiado', 'monto_total_con_interes',
        'cantidad_cuotas', 'tasa_interes_mensual', 'tasa_mora_diaria',
        'fecha_primer_vencimiento',
    ];

    protected function casts(): array
    {
        return [
            'monto_anticipo'           => 'decimal:2',
            'monto_financiado'         => 'decimal:2',
            'monto_total_con_interes'  => 'decimal:2',
            'cantidad_cuotas'          => 'integer',
            'tasa_interes_mensual'     => 'decimal:2',
            'tasa_mora_diaria'         => 'decimal:2',
            'fecha_primer_vencimiento' => 'date',
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function cuotas(): HasMany
    {
        return $this->hasMany(Cuota::class, 'financiamiento_id')->orderBy('numero_cuota');
    }

    public function getTotalPagadoAttribute(): float
    {
        return (float) $this->cuotas()->sum('monto_pagado');
    }

    public function getSaldoAttribute(): float
    {
        return (float) $this->monto_total_con_interes - $this->total_pagado;
    }

    public function getCuotasPendientesAttribute(): int
    {
        return $this->cuotas()
            ->whereIn('estado', [
                EstadoCuota::PENDIENTE->value,
                EstadoCuota::PARCIAL->value,
                EstadoCuota::VENCIDA->value,
            ])->count();
    }

    public function estaCancelado(): bool
    {
        return $this->cuotas_pendientes === 0;
    }
}
