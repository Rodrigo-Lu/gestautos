<?php

namespace App\Models;

use App\Enums\EstadoVenta;
use App\Enums\Moneda;
use App\Enums\TipoPago;
use App\Enums\TipoVenta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'vehiculo_id', 'cliente_id', 'usuario_id', 'tasacion_permuta_id',
        'fecha', 'precio_venta', 'monto_permuta', 'moneda', 'tipo_cambio',
        'tipo_venta', 'forma_pago_anticipo', 'estado', 'observaciones',
        'motivo_anulacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha'               => 'date',
            'precio_venta'        => 'decimal:2',
            'monto_permuta'       => 'decimal:2',
            'tipo_cambio'         => 'decimal:4',
            'moneda'              => Moneda::class,
            'tipo_venta'          => TipoVenta::class,
            'forma_pago_anticipo' => TipoPago::class,
            'estado'              => EstadoVenta::class,
        ];
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tasacionPermuta(): BelongsTo
    {
        return $this->belongsTo(Tasacion::class, 'tasacion_permuta_id');
    }

    public function financiamiento(): HasOne
    {
        return $this->hasOne(Financiamiento::class, 'venta_id');
    }

    public function comprobantes(): HasMany
    {
        return $this->hasMany(Comprobante::class, 'venta_id');
    }

    public function scopeVigentes(Builder $q): Builder
    {
        return $q->whereIn('estado', [
            EstadoVenta::VIGENTE->value,
            EstadoVenta::FINALIZADA->value,
        ]);
    }

    public function scopeEntre(Builder $q, ?string $desde, ?string $hasta): Builder
    {
        return $q->when($desde, fn (Builder $q) => $q->whereDate('fecha', '>=', $desde))
                 ->when($hasta, fn (Builder $q) => $q->whereDate('fecha', '<=', $hasta));
    }

    public function esFinanciada(): bool
    {
        return $this->tipo_venta === TipoVenta::FINANCIADO;
    }

    public function puedeAnularse(): bool
    {
        return $this->estado !== EstadoVenta::ANULADA;
    }

    /** Precio menos lo que se entrego en permuta. */
    public function getMontoNetoAttribute(): float
    {
        return (float) $this->precio_venta - (float) $this->monto_permuta;
    }

    /** Importe convertido a guaranies, para sumar ventas de distintas monedas. */
    public function getMontoEnGuaraniesAttribute(): float
    {
        if ($this->moneda === Moneda::GS) {
            return (float) $this->precio_venta;
        }

        return (float) $this->precio_venta * (float) ($this->tipo_cambio ?: 0);
    }
}
