<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesional extends Model
{
    protected $table = 'profesionales';


    protected $fillable = [
        'descripcion',
        'nombre_comercial',
        'telefono_comercial',
        'foto',
        'localidad_id',

        // Compatibilidad temporal con el esquema anterior.
        'provincia',
        'ciudad',
    ];

    protected function casts(): array
    {
        return [
            'publicado' => 'boolean',
        ];
    }


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

    public function resenas(): HasMany {
        return $this->hasMany(Resena::class);
    }

    // Un profesional recibe muchas solicitudes de trabajo (1:N)
    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }

    public function localidadesCobertura(): BelongsToMany
    {
        return $this->belongsToMany(Localidad::class)
            ->withTimestamps();
    }

    public function localidadPrincipal(): BelongsTo
    {
        return $this->belongsTo(Localidad::class, 'localidad_id');
    }

}