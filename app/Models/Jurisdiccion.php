<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurisdiccion extends Model
{
    protected $table = 'jurisdicciones';

    protected $fillable = [
        'georef_id',
        'nombre',
    ];

    public function localidades(): HasMany
    {
        return $this->hasMany(Localidad::class);
    }
}
