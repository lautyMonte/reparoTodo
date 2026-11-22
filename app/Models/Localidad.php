<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Localidad extends Model
{
    protected $table = 'localidades';

    protected $fillable = [
        'jurisdiccion_id',
        'georef_id',
        'nombre',
    ];

    public function jurisdiccion(): BelongsTo
    {
        return $this->belongsTo(Jurisdiccion::class);
    }

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class)
            ->withTimestamps();
    }
}
