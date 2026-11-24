<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function destroy(Request $request, Resena $resena): RedirectResponse
    {
        // Solo el autor puede eliminar su reseña.
        abort_unless($resena->user_id === $request->user()->id, 403);

        // Guardamos el profesional para volver a su perfil.
        $profesionalId = $resena->profesional_id;

        // Eliminación mediante Eloquent.
        $resena->delete();

        return redirect()
            ->route('profesional.show', $profesionalId)
            ->with('exito', 'Reseña eliminada correctamente.');
    }
}