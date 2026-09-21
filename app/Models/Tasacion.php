<?php

namespace App\Models;

use App\Enums\EstadoTasacion;
use App\Enums\Moneda;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tasacion extends Model
{
    use HasFactory;

    protected $table = 'tasaciones';

    protected $fillable = [
        'nombre_contacto', 'telefono_contacto', 'marca', 'modelo', 'anio',
        'kilometraje', 'monto_ofrecido', 'moneda', 'fecha', 'estado',
        'observaciones', 'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'anio'           => 'integer',
            'kilometraje'    => 'integer',
            'monto_ofrecido' => 'decimal:2',
            'moneda'         => Moneda::class,
            'estado'         => EstadoTasacion::class,
            'fecha'          => 'date',
        ];
    }

    public function evaluador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /** Vehiculo que ingreso al stock a partir de esta tasacion. */
    public function vehiculoGenerado(): HasOne
    {
        return $this->hasOne(Vehiculo::class, 'tasacion_origen_id');
    }

    /** Venta en la que esta tasacion se uso como permuta. */
    public function ventaPermuta(): HasOne
    {
        return $this->hasOne(Venta::class, 'tasacion_permuta_id');
    }

    public function getDescripcionAttribute(): string
    {
        return "{$this->marca} {$this->modelo} {$this->anio}";
    }
}
