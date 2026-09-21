<?php

namespace App\Models;

use App\Enums\Moneda;
use App\Enums\TipoComprobante;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    protected $table = 'comprobantes';

    protected $fillable = [
        'numero', 'tipo', 'fecha_emision', 'monto', 'moneda',
        'ruta_archivo', 'venta_id', 'cuota_id', 'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'date',
            'monto'         => 'decimal:2',
            'moneda'        => Moneda::class,
            'tipo'          => TipoComprobante::class,
        ];
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function cuota(): BelongsTo
    {
        return $this->belongsTo(Cuota::class, 'cuota_id');
    }

    public function emisor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
