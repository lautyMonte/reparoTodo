<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'leida_profesional',
        'cierre_propuesto_por',
        'cierre_tipo',
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

    // Una solicitud tiene un presupuesto (1:1)
    public function presupuesto(): HasOne
    {
        return $this->hasOne(Presupuesto::class);
    }

    // Quien propuso el cierre que esta esperando respuesta (un usuario)
    public function proponente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cierre_propuesto_por');
    }

    // Hay una propuesta de cierre esperando respuesta?
    public function tieneCierrePendiente(): bool
    {
        return $this->cierre_propuesto_por !== null;
    }

    // La otra persona de la solicitud: si sos el cliente es el profesional, y al reves
    public function otraPersona(User $usuario): User
    {
        return $usuario->id === $this->cliente_id ? $this->profesional->user : $this->cliente;
    }

    protected function casts(): array
    {
        return [
            'fecha_deseada' => 'date',
            'leida_profesional' => 'boolean',
            'cierre_propuesto_por' => 'integer',
        ];
    }
}