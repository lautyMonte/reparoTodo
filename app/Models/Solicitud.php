<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Solicitud extends Model
{
    // Laravel pluraliza en ingles y buscaria "solicituds", asi que le decimos el nombre real
    protected $table = 'solicitudes';

    protected $fillable = [
        'cliente_id',
        'profesional_id',
        'titulo',
        'descripcion',
        'zona',
        'fecha_deseada',
        'estado',
    ];

    // Cada solicitud la hizo un cliente (un usuario). La columna se llama cliente_id, no user_id,
    // asi que hay que indicarla
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    // Y va dirigida a un profesional (el lado "1" de la relacion 1:N)
    public function profesional(): BelongsTo
    {
        return $this->belongsTo(Profesional::class);
    }

    // Una solicitud tiene muchas fotos (1:N)
    public function fotos(): HasMany
    {
        return $this->hasMany(SolicitudFoto::class);
    }

    // Para que fecha_deseada se maneje como fecha y no como texto
    protected function casts(): array
    {
        return [
            'fecha_deseada' => 'date',
        ];
    }
}