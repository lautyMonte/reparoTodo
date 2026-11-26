<?php

namespace App\Policies;

use App\Models\Resena;
use App\Models\Solicitud;
use App\Models\User;

class ResenaPolicy
{
    // Solo el cliente titular de un trabajo finalizado puede reseñarlo.
    public function create(User $usuario, Solicitud $solicitud): bool
    {
        return $usuario->tipo === 'cliente'
            && $solicitud->cliente_id === $usuario->id
            && $solicitud->estado === 'finalizada';
    }

    // Solo el autor puede modificar su reseña.
    public function update(User $usuario, Resena $resena): bool
    {
        return $this->create($usuario, $resena->solicitud);
    }

    // Solo el autor puede eliminar su reseña.
    public function delete(User $usuario, Resena $resena): bool
    {
        return $usuario->tipo === 'cliente'
            && $resena->solicitud->cliente_id === $usuario->id;
    }
}