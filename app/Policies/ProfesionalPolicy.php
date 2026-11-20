<?php

namespace app\Policies;

use App\Models\User;
use App\Models\Profesional;
use Illuminate\Auth\Access\Response;

class ProfesionalPolicy
{
    // /**si se quiere modificar los datos del profesional, deberia estar en un can dentro del blade perfil de profesional */
    // public function updateProfesional(User $user, Profesional $profesional): boolean
    // {
    //     return $user->id === $profesional->user_id;
    // }
}