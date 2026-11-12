<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Oficio extends Model
{
    //
    protected $table = 'oficios';

    protected $fillable = [
        'tipo'
    ];

    //relacion muchos a muchos con profesionales
    public function profesionales(): BelongsToMany
    {
        return $this->belongsToMany(Profesional::class);
    }
}
