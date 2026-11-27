<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Oficio extends Model
{
    protected $fillable = [
        'tipo',
    ];

    public function nombreParaGenero(?string $genero): string
    {
        // Si el genero es femenino, muestra los oficios correspondientes al genero
        if ($genero !== 'f') {
            return $this->tipo;
        }

        return match ($this->tipo) {
            'Plomero' => 'Plomera',
            'Cerrajero' => 'Cerrajera',
            'Carpintero' => 'Carpintera',
            'Fletero' => 'Fletera',
            'Pintor' => 'Pintora',
            default => $this->tipo,
        };
    }

    // Relacion muchos a muchos con profesionales
    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class)->withTimestamps();
    }
}
