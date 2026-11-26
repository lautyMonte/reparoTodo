<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Profesional extends Model
{
    protected $table = 'profesionales';

    protected $fillable = [];

    // Relacion muchos a muchos con oficios
    public function oficios(): BelongsToMany
    {
        return $this->belongsToMany(Oficio::class)->withTimestamps();
    }

    // Relacion uno a uno con usuario (cada profesional pertenece a un solo usuario)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Reseñas recibidas mediante las solicitudes de trabajo.
    public function resenas(): HasManyThrough
    {
        return $this->hasManyThrough(
            Resena::class,
            Solicitud::class,
            'profesional_id',
            'solicitud_id'
        );
    }

    // Un profesional recibe muchas solicitudes de trabajo (1:N)
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }

    // Un profesional tiene muchas ciudades
    public function ciudades(): BelongsToMany
    {
        return $this->belongsToMany(Ciudad::class)->withTimestamps();
    }
}
