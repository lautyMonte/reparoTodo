<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ciudad extends Model
{
    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'provincia_id',
    ];

    public function provincia(): BelongsTo
    {
        return $this->belongsTo(Provincia::class);
    }

    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class)->withTimestamps();
    }
}