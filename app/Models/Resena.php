<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resena extends Model
{
    protected $fillable = [
        'profesional_id',
        'user_id',
        'puntaje',
        'comentario',
    ];

    // Cada resena pertenece a un profesional (el lado "1" de la relacion 1:N)
    public function profesional(): BelongsTo
    {
        return $this->belongsTo(Profesional::class);
    }

    // Y fue escrita por un usuario (el cliente). Por convencion busca la columna user_id
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}