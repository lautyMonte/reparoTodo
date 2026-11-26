<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class ResenaController extends Controller
{
    public function destroy(Resena $resena): RedirectResponse
    {
        // Solo el autor puede eliminar su reseña (lo decide ResenaPolicy).
        Gate::authorize('delete', $resena);

        // Guardamos el profesional para volver a su perfil.
        $profesionalId = $resena->profesional_id;

        // Eliminación mediante Eloquent.
        $resena->delete();

        return redirect()
            ->route('profesional.show', $profesionalId)
            ->with('exito', 'Reseña eliminada correctamente.');
    }
}