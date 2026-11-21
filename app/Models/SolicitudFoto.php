<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudFoto extends Model
{
    protected $fillable = [
        'solicitud_id',
        'ruta',
    ];

    // Cada foto pertenece a una solicitud
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }
}