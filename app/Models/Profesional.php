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
        'provincia',
        'ciudad',
    ];

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

}