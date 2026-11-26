<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Publicacion extends Model
{
    protected $table = 'publicaciones';

    protected $fillable = [
        'solicitud_id',
        'titulo',
        'descripcion',
    ];

    // Cada publicación pertenece a una solicitud.
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    // Una publicación puede tener varias fotografías.
    public function fotos(): HasMany
    {
        return $this->hasMany(PublicacionFoto::class);
    }
}