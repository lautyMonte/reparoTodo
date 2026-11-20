<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Oficio extends Model
{
    protected $fillable = [
        'tipo'
    ];

    // Relacion muchos a muchos con profesionales
    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class)->withTimestamps();
    }
}
