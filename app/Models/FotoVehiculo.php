<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FotoVehiculo extends Model
{
    protected $table = 'fotos_vehiculo';

    protected $fillable = ['vehiculo_id', 'ruta', 'orden', 'es_principal'];

    protected function casts(): array
    {
        return [
            'es_principal' => 'boolean',
            'orden'        => 'integer',
        ];
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id');
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->ruta);
    }
}
