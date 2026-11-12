<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Profesional extends Model
{
    protected $table = 'profesionales';
    
    protected $fillable = [
        'provincia',
        'ciudad',
    ];

    //relacion muchos a muchos con oficios
    public function oficios(): BelongsToMany
    {
        return $this->belongsToMany(Oficio::class);
    }

    //relacion uno a muchos con usuario
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

