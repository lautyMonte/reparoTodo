<?php

namespace App\Policies;

use App\Models\Publicacion;
use App\Models\Solicitud;
use App\Models\User;

class PublicacionPolicy
{
    // Solo el profesional titular de un trabajo finalizado puede publicarlo.
    public function create(User $usuario, Solicitud $solicitud): bool
    {
        return $this->esPropietario($usuario, $solicitud)
            && $solicitud->estado === 'finalizada';
    }

    // Solo el profesional propietario puede modificar su publicación.
    public function update(User $usuario, Publicacion $publicacion): bool
    {
        return $this->create($usuario, $publicacion->solicitud);
    }

    // Solo el profesional propietario puede eliminar su publicación.
    public function delete(User $usuario, Publicacion $publicacion): bool
    {
        return $this->esPropietario($usuario, $publicacion->solicitud);
    }

    // Comprueba que el usuario sea titular de la solicitud.
    private function esPropietario(User $usuario, Solicitud $solicitud): bool
    {
        return $usuario->tipo === 'profesional'
            && $usuario->profesional !== null
            && $usuario->profesional->id === $solicitud->profesional_id;
    }
}