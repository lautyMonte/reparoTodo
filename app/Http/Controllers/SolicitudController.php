<?php

namespace App\Http\Controllers;

use App\Models\Profesional;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    /** Muestra el formulario para pedirle un trabajo a un profesional */
    public function create(int $id): View
    {
        $profesional = Profesional::with('user')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->findOrFail($id);

        return view('solicitud.create', [
            'profesional' => $profesional,
            'titulo' => 'Solicitar trabajo',
        ]);
    }

    /** Valida y guarda la solicitud con sus fotos */
    public function store(Request $request, int $id): RedirectResponse
    {
        $profesional = Profesional::findOrFail($id);

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:80'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'zona' => ['nullable', 'string', 'max:100'],
            'fecha_deseada' => ['nullable', 'date', 'after_or_equal:today'],
            'fotos' => ['nullable', 'array', 'max:3'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'titulo.required' => 'El nombre del trabajo es obligatorio',
            'titulo.max' => 'El nombre del trabajo puede tener hasta 80 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.max' => 'La descripción puede tener hasta 1000 caracteres',
            'zona.max' => 'La zona puede tener hasta 100 caracteres',
            'fecha_deseada.date' => 'La fecha deseada no es válida',
            'fecha_deseada.after_or_equal' => 'La fecha deseada no puede ser anterior a hoy',
            'fotos.max' => 'Podés subir hasta 3 fotos',
            'fotos.*.image' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
            'fotos.*.mimes' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
            'fotos.*.max' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
            'fotos.*.uploaded' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
        ]);

        // El cliente sale de la sesion (no del formulario) para que nadie pueda pedir un trabajo a nombre de otro
        $solicitud = Solicitud::create([
            'cliente_id' => $request->user()->id,
            'profesional_id' => $profesional->id,
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'zona' => $datos['zona'] ?? null,
            'fecha_deseada' => $datos['fecha_deseada'] ?? null,
        ]);

        // Cada foto se guarda en storage/app/public/solicitudes y en la base queda solo su ruta
        foreach ($request->file('fotos', []) as $foto) {
            $solicitud->fotos()->create([
                'ruta' => $foto->store('solicitudes', 'public'),
            ]);
        }

        return redirect()
            ->route('profesional.show', $profesional->id)
            ->with('exito', 'Solicitud enviada. '.$profesional->user->usuario.' la va a revisar y te va a responder.');
    }
}