<?php

namespace App\Policies;

use App\Models\Resena;
use App\Models\User;

class ResenaPolicy
{
    // Solo el autor de la reseña puede eliminarla
    public function delete(User $usuario, Resena $resena): bool
    {
        return $resena->user_id === $usuario->id;
    }
}