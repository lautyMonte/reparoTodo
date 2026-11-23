<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presupuesto extends Model
{
    protected $fillable = [
        'solicitud_id',
        'monto',
        'plazo_dias',
        'detalle',
    ];

    // El presupuesto pertenece a una solicitud
    public function solicitud(): BelongsTo
    {
        return $this->belongsTo(Solicitud::class);
    }
}