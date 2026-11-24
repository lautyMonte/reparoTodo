<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
   public function update(User $usuarioAutenticado, User $usuario)
    {
        return $usuarioAutenticado->id === $usuario->id
            && $usuarioAutenticado->tipo === 'cliente';
    }
}
