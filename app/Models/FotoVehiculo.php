<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        // Use the current request host/port instead of the APP_URL value.
        // This keeps images working when the app runs on :8000, a LAN IP, or
        // behind a domain different from the development URL.
        return asset('storage/'.ltrim($this->ruta, '/'));
    }
}
