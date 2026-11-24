<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Profesional;
use Illuminate\Auth\Access\Response;

class ProfesionalPolicy
{
    public function update(User $usuario, Profesional $profesional)
    {
        return $usuario->tipo === 'profesional'
            && $profesional->user_id === $usuario->id;
    }
}