<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicacionFoto extends Model
{
    protected $table = 'publicacion_fotos';

    protected $fillable = [
        'publicacion_id',
        'ruta',
    ];

    // Cada fotografía pertenece a una publicación.
    public function publicacion(): BelongsTo
    {
        return $this->belongsTo(Publicacion::class);
    }
}