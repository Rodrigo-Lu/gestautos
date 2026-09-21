<?php

namespace App\Models;

use App\Enums\EstadoVehiculo;
use App\Enums\Moneda;
use App\Enums\TipoCombustible;
use App\Enums\TipoTransmision;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehiculos';

    protected $fillable = [
        'codigo_publicacion', 'marca', 'modelo', 'version', 'anio',
        'precio', 'precio_compra', 'moneda', 'kilometraje', 'color',
        'numero_chasis', 'transmision', 'combustible', 'acepta_permuta',
        'fecha_ingreso', 'activo', 'estado', 'descripcion', 'tasacion_origen_id',
    ];

    protected function casts(): array
    {
        return [
            'anio'           => 'integer',
            'kilometraje'    => 'integer',
            'precio'         => 'decimal:2',
            'precio_compra'  => 'decimal:2',
            'moneda'         => Moneda::class,
            'transmision'    => TipoTransmision::class,
            'combustible'    => TipoCombustible::class,
            'estado'         => EstadoVehiculo::class,
            'acepta_permuta' => 'boolean',
            'activo'         => 'boolean',
            'fecha_ingreso'  => 'date',
        ];
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(FotoVehiculo::class, 'vehiculo_id')->orderBy('orden');
    }

    public function fotoPrincipal(): HasOne
    {
        return $this->hasOne(FotoVehiculo::class, 'vehiculo_id')
                    ->where('es_principal', true);
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'vehiculo_id');
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class, 'vehiculo_id');
    }

    public function tasacionOrigen(): BelongsTo
    {
        return $this->belongsTo(Tasacion::class, 'tasacion_origen_id');
    }

    public function scopeDisponibles(Builder $q): Builder
    {
        return $q->where('activo', true)
                 ->where('estado', EstadoVehiculo::DISPONIBLE->value);
    }

    /**
     * Filtro unico para el panel y para el catalogo publico.
     *
     * @param  array<string,mixed>  $f
     */
    public function scopeFiltrar(Builder $q, array $f): Builder
    {
        return $q
            ->when(filled($f['q'] ?? null), function (Builder $q) use ($f) {
                $t = '%'.trim($f['q']).'%';
                $q->where(function (Builder $sub) use ($t) {
                    $sub->where('marca', 'like', $t)
                        ->orWhere('modelo', 'like', $t)
                        ->orWhere('version', 'like', $t)
                        ->orWhere('codigo_publicacion', 'like', $t)
                        ->orWhere('numero_chasis', 'like', $t);
                });
            })
            ->when(filled($f['marca'] ?? null), fn (Builder $q) => $q->where('marca', $f['marca']))
            ->when(filled($f['estado'] ?? null), fn (Builder $q) => $q->where('estado', $f['estado']))
            ->when(filled($f['anio_desde'] ?? null), fn (Builder $q) => $q->where('anio', '>=', $f['anio_desde']))
            ->when(filled($f['anio_hasta'] ?? null), fn (Builder $q) => $q->where('anio', '<=', $f['anio_hasta']))
            ->when(filled($f['precio_desde'] ?? null), fn (Builder $q) => $q->where('precio', '>=', $f['precio_desde']))
            ->when(filled($f['precio_hasta'] ?? null), fn (Builder $q) => $q->where('precio', '<=', $f['precio_hasta']))
            ->when(filled($f['combustible'] ?? null), fn (Builder $q) => $q->where('combustible', $f['combustible']))
            ->when(filled($f['transmision'] ?? null), fn (Builder $q) => $q->where('transmision', $f['transmision']));
    }

    public function getDescripcionCortaAttribute(): string
    {
        return trim("{$this->marca} {$this->modelo} {$this->version} {$this->anio}");
    }

    public function precioFormateado(): string
    {
        return $this->moneda->formatear($this->precio);
    }

    public function puedeVenderse(): bool
    {
        return $this->activo && $this->estado !== EstadoVehiculo::VENDIDO;
    }
}
