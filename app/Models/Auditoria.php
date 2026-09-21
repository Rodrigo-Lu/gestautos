<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Auditoria extends Model
{
    protected $table = 'auditorias';

    public $timestamps = false;

    protected $fillable = [
        'usuario_id', 'modelo', 'modelo_id', 'accion',
        'datos_antes', 'datos_despues', 'ip', 'created_at',
    ];

    protected function casts(): array
    {
        return [
            'datos_antes'   => 'array',
            'datos_despues' => 'array',
            'created_at'    => 'datetime',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
