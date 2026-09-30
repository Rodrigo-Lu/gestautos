<?php

namespace App\Models;

use App\Enums\EstadoConsulta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consulta extends Model
{
    protected $table = 'consultas';

    protected $fillable = [
        'nombre', 'telefono', 'email', 'mensaje', 'fecha', 'fecha_contacto',
        'estado', 'origen', 'vehiculo_id', 'cliente_id', 'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha'          => 'date',
            'fecha_contacto' => 'date',
            'estado'         => EstadoConsulta::class,
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

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function scopeNuevas(Builder $q): Builder
    {
        return $q->where('estado', EstadoConsulta::NUEVA->value);
    }
}
