<?php

namespace App\Http\Controllers;

use App\Models\Resena;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class ResenaController extends Controller
{
    public function store(Request $request, Solicitud $solicitud): RedirectResponse
    {
        Gate::authorize('create', [Resena::class, $solicitud]);

        $datos = $request->validate([
            'puntaje' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $resena = $solicitud->resena()->firstOrCreate([], $datos);

        abort_unless(
            $resena->wasRecentlyCreated,
            409,
            'Esta solicitud ya tiene una reseña.'
        );

        return redirect()
            ->route('solicitud.cliente.show', $solicitud->id)
            ->with('exito', 'Reseña publicada correctamente.');
    }

    public function update(Request $request, Resena $resena): RedirectResponse
    {
        Gate::authorize('update', $resena);

        $datos = $request->validate([
            'puntaje' => ['required', 'integer', 'between:1,5'],
            'comentario' => ['nullable', 'string', 'max:1000'],
        ]);

        $resena->update($datos);

        if ($request->input('origen') === 'mis_solicitudes') {
            return redirect()
                ->route('solicitud.cliente.show', $resena->solicitud_id)
                ->with('exito', 'Reseña actualizada correctamente.');
        }

        return redirect()
            ->route('profesional.show', [
                'id' => $resena->solicitud->profesional_id,
                'tab' => 'resenas',
            ])
            ->with('exito', 'Reseña actualizada correctamente.');
    }

    public function destroy(Resena $resena): RedirectResponse
    {
        Gate::authorize('delete', $resena);

        $profesionalId = $resena->solicitud->profesional_id;

        $resena->delete();

        return redirect()
            ->route('profesional.show', [
                'id' => $profesionalId,
                'tab' => 'resenas',
            ])
            ->with('exito', 'Reseña eliminada correctamente.');
    }
}