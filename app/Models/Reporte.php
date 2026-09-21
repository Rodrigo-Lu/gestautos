<?php

namespace App\Models;

use App\Enums\TipoReporte;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reporte extends Model
{
    protected $table = 'reportes';

    protected $fillable = [
        'tipo', 'fecha_generacion', 'fecha_desde', 'fecha_hasta',
        'ruta_archivo', 'parametros', 'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'tipo'             => TipoReporte::class,
            'fecha_generacion' => 'date',
            'fecha_desde'      => 'date',
            'fecha_hasta'      => 'date',
            'parametros'       => 'array',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
