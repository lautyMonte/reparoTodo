<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    // Laravel pluraliza en ingles y buscaria "notificacions", asi que le decimos el nombre real
    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'solicitud_id',
        'tipo',
        'mensaje',
        'leida',
    ];

    // A quien va dirigida la notificacion
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // De que solicitud habla (sirve para abrirla al tocar la notificacion)
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }

    // Para que leida se maneje como verdadero/falso y no como 0 o 1
    protected function casts(): array
    {
        return [
            'leida' => 'boolean',
        ];
    }
}