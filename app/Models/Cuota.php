<?php

namespace App\Models;

use App\Enums\EstadoCuota;
use App\Enums\TipoAlerta;
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

    public function scopeParaAvisarHoy(Builder $q): Builder
    {
        $hoy = now()->toDateString();
        $manana = now()->addDay()->toDateString();

        return $q
            ->whereIn('estado', [
                EstadoCuota::PENDIENTE->value,
                EstadoCuota::PARCIAL->value,
            ])
            ->where(function (Builder $q) use ($hoy, $manana) {
                $q->where(function (Builder $q) use ($hoy) {
                    $q->whereDate('fecha_vencimiento', $hoy)
                        ->whereDoesntHave('alertas', function (Builder $a) {
                            $a->where('tipo', TipoAlerta::VENCE_HOY->value)
                                ->whereNotNull('avisado_at');
                        });
                })->orWhere(function (Builder $q) use ($manana) {
                    $q->whereDate('fecha_vencimiento', $manana)
                        ->whereDoesntHave('alertas', function (Builder $a) {
                            $a->where('tipo', TipoAlerta::POR_VENCER->value)
                                ->whereNotNull('avisado_at');
                        });
                });
            });
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

    public function tipoAlertaAviso(): ?TipoAlerta
    {
        $hoy = now()->toDateString();
        $fecha = $this->fecha_vencimiento->toDateString();

        return match (true) {
            $this->estado === EstadoCuota::VENCIDA => TipoAlerta::VENCIDA,
            $fecha === $hoy => TipoAlerta::VENCE_HOY,
            $this->fecha_vencimiento->isPast() => TipoAlerta::VENCIDA,
            default => TipoAlerta::POR_VENCER,
        };
    }

    public function alertaAviso(): ?Alerta
    {
        $tipo = $this->tipoAlertaAviso();

        if ($this->relationLoaded('alertas')) {
            return $this->alertas->first(
                fn (Alerta $alerta) => $alerta->tipo === $tipo
            );
        }

        return $this->alertas()->where('tipo', $tipo->value)->first();
    }

    public function whatsappMessage(?float $mora = null): string
    {
        $venta = $this->financiamiento->venta;
        $cliente = $venta->cliente;
        $vehiculo = $venta->vehiculo->descripcion_corta;
        $nombre = $cliente->nombre;
        $cuota = "{$this->numero_cuota}/{$this->financiamiento->cantidad_cuotas}";
        $saldo = number_format($this->saldo, 0, ',', '.');
        $fecha = $this->fecha_vencimiento->format('d/m/Y');
        $tipo = $this->tipoAlertaAviso();

        if ($tipo === TipoAlerta::VENCE_HOY) {
            return "Hola {$nombre}, le recordamos que hoy vence la cuota {$cuota} de su {$vehiculo}. Saldo: Gs. {$saldo}. Gracias. — JP Automotores";
        }

        if ($tipo === TipoAlerta::VENCIDA) {
            return 'Hola '.$nombre.", su cuota {$cuota} de su {$vehiculo} venció el {$fecha}. Saldo: Gs. {$saldo} + mora Gs. ".number_format($mora ?? (float) $this->monto_mora, 0, ',', '.').'. Por favor comuníquese con nosotros. — JP Automotores';
        }

        $momento = $this->fecha_vencimiento->toDateString() === now()->addDay()->toDateString()
            ? 'mañana'
            : "el {$fecha}";

        return "Hola {$nombre}, le recordamos que {$momento} vence la cuota {$cuota} de su {$vehiculo}. Saldo: Gs. {$saldo}. Gracias. — JP Automotores";
    }

    public function whatsappLink(?float $mora = null): ?string
    {
        $cliente = $this->financiamiento?->venta?->cliente;
        $telefono = $cliente?->whatsappTelefono();

        if ($this->estaPagada() || ! $telefono) {
            return null;
        }

        return 'https://wa.me/'.$telefono.'?text='.urlencode($this->whatsappMessage($mora));
    }

    public function getTotalAPagarAttribute(): float
    {
        return round($this->saldo + (float) $this->monto_mora, 2);
    }
}
