<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resena extends Model
{
    protected $fillable = [
        'solicitud_id',
        'puntaje',
        'comentario',
    ];

    // Cada reseña pertenece a una solicitud.
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }
}