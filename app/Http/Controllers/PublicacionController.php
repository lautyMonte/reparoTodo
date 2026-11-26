<?php

namespace App\Http\Controllers;

use App\Models\Publicacion;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PublicacionController extends Controller
{
    public function store(Request $request, Solicitud $solicitud): RedirectResponse
    {
        Gate::authorize('create', [Publicacion::class, $solicitud]);

        $datos = $request->validateWithBag('publicacion', [
            'titulo' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string', 'max:2000'],
            'fotos' => ['sometimes', 'array', 'max:5'],
            'fotos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], $this->mensajesValidacion());

        $rutasGuardadas = [];

        try {
            DB::transaction(function () use ($solicitud, $datos, &$rutasGuardadas) {

                $publicacion = $solicitud->publicacion()->firstOrCreate([], [
                    'titulo' => $datos['titulo'],
                    'descripcion' => $datos['descripcion'],
                ]);

                abort_unless(
                    $publicacion->wasRecentlyCreated,
                    409,
                    'Esta solicitud ya tiene una publicación.'
                );

                foreach ($datos['fotos'] ?? [] as $foto) {

                    $ruta = $foto->store('publicaciones', 'public');

                    if ($ruta === false) {
                        throw new \RuntimeException(
                            'No se pudo almacenar la fotografía.'
                        );
                    }

                    $rutasGuardadas[] = $ruta;

                    $publicacion->fotos()->create([
                        'ruta' => $ruta,
                    ]);
                }
            });

        } catch (\Throwable $error) {

            $this->eliminarArchivos($rutasGuardadas);

            throw $error;
        }

        return redirect()
            ->route('solicitud.show', $solicitud->id)
            ->with('exito', 'Trabajo publicado correctamente.');
    }

    public function update(Request $request, Publicacion $publicacion): RedirectResponse
    {
        Gate::authorize('update', $publicacion);

        $datos = $request->validateWithBag('publicacion', [
            'titulo' => ['required', 'string', 'max:100'],
            'descripcion' => ['required', 'string', 'max:2000'],
            'fotos' => ['sometimes', 'array', 'max:5'],
            'fotos.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'eliminar_fotos' => ['sometimes', 'array', 'max:5'],
            'eliminar_fotos.*' => ['required', 'integer', 'distinct'],
        ], $this->mensajesValidacion());

        $rutasNuevas = [];

        try {
            $rutasEliminar = DB::transaction(function () use (
                $publicacion,
                $datos,
                &$rutasNuevas
            ) {

                // Bloquear la publicación durante la edición.
                $publicacion = Publicacion::lockForUpdate()
                    ->findOrFail($publicacion->id);

                $idsEliminar = $datos['eliminar_fotos'] ?? [];
                $fotosNuevas = $datos['fotos'] ?? [];

                // Buscar únicamente fotografías de esta publicación.
                $fotosEliminar = $publicacion->fotos()
                    ->whereIn('id', $idsEliminar)
                    ->get();

                if ($fotosEliminar->count() !== count($idsEliminar)) {
                    throw ValidationException::withMessages([
                        'eliminar_fotos' => 'Las fotografías seleccionadas no son válidas.',
                    ])->errorBag('publicacion');
                }

                // Comprobar la cantidad final de fotografías.
                $cantidadFinal = $publicacion->fotos()->count()
                    - $fotosEliminar->count()
                    + count($fotosNuevas);

                if ($cantidadFinal > 5) {
                    throw ValidationException::withMessages([
                        'fotos' => 'La publicación admite un máximo de cinco fotografías.',
                    ])->errorBag('publicacion');
                }

                // Actualizar los datos de la publicación.
                $publicacion->update([
                    'titulo' => $datos['titulo'],
                    'descripcion' => $datos['descripcion'],
                ]);

                // Obtener las rutas de las fotografías eliminadas.
                $rutasEliminar = $fotosEliminar->pluck('ruta')->all();

                // Eliminar los registros de la base de datos.
                $publicacion->fotos()
                    ->whereIn('id', $idsEliminar)
                    ->delete();

                // Almacenar las fotografías nuevas.
                foreach ($fotosNuevas as $foto) {

                    $ruta = $foto->store('publicaciones', 'public');

                    if ($ruta === false) {
                        throw new \RuntimeException(
                            'No se pudo almacenar la fotografía.'
                        );
                    }

                    $rutasNuevas[] = $ruta;

                    $publicacion->fotos()->create([
                        'ruta' => $ruta,
                    ]);
                }

                return $rutasEliminar;
            });

        } catch (\Throwable $error) {

            $this->eliminarArchivos($rutasNuevas);

            throw $error;
        }

        // Eliminar los archivos anteriores después del commit.
        $archivosEliminados = $this->eliminarArchivos($rutasEliminar);

        $respuesta = $this->redirigir(
            $request,
            $publicacion->solicitud,
            'Publicación actualizada correctamente.'
        );

        if (!$archivosEliminados) {
            $respuesta->with(
                'advertencia',
                'La publicación fue actualizada, pero algunos archivos anteriores no pudieron borrarse.'
            );
        }

        return $respuesta;
    }

    public function destroy(Request $request, Publicacion $publicacion): RedirectResponse
    {
        Gate::authorize('delete', $publicacion);

        $solicitud = $publicacion->solicitud;

        $rutasEliminar = DB::transaction(function () use ($publicacion) {

            // Bloquear la publicación durante la eliminación.
            $publicacion = Publicacion::lockForUpdate()
                ->findOrFail($publicacion->id);

            // Recuperar las rutas antes de eliminar los registros.
            $rutas = $publicacion->fotos()
                ->pluck('ruta')
                ->all();

            // Eliminar la publicación y sus registros de fotografías.
            $publicacion->delete();

            return $rutas;
        });

        // Eliminar los archivos físicos después del commit.
        $archivosEliminados = $this->eliminarArchivos($rutasEliminar);

        $respuesta = $this->redirigir(
            $request,
            $solicitud,
            'Publicación eliminada correctamente.'
        );

        if (!$archivosEliminados) {
            $respuesta->with(
                'advertencia',
                'La publicación fue eliminada, pero algunos archivos no pudieron borrarse.'
            );
        }

        return $respuesta;
    }
    private function mensajesValidacion(): array
    {
        return [
            'titulo.required' => 'Ingresá un título para el trabajo.',
            'titulo.string' => 'Ingresá un título válido.',
            'titulo.max' => 'El título no puede superar los 100 caracteres.',

            'descripcion.required' => 'Ingresá una descripción del trabajo.',
            'descripcion.string' => 'Ingresá una descripción válida.',
            'descripcion.max' => 'La descripción no puede superar los 2000 caracteres.',

            'fotos.array' => 'La selección de fotografías no es válida.',
            'fotos.max' => 'La publicación admite un máximo de cinco fotografías.',
            'fotos.*.required' => 'Seleccioná fotografías válidas.',
            'fotos.*.image' => 'Seleccioná fotografías en formato JPG, PNG o WebP.',
            'fotos.*.mimes' => 'Seleccioná fotografías en formato JPG, PNG o WebP.',
            'fotos.*.max' => 'Cada fotografía puede pesar hasta 2 MB.',
            'fotos.*.uploaded' => 'No se pudieron cargar las fotografías; revisá que no superen los 2 MB por archivo.',

            'eliminar_fotos.array' => 'La selección de fotografías no es válida.',
            'eliminar_fotos.max' => 'La selección de fotografías no es válida.',
            'eliminar_fotos.*.required' => 'Seleccioná fotografías válidas.',
            'eliminar_fotos.*.integer' => 'La selección de fotografías no es válida.',
            'eliminar_fotos.*.distinct' => 'No se permiten fotografías repetidas en la selección.',
        ];
    }
    private function eliminarArchivos(array $rutas): bool
    {
        if (empty($rutas)) {
            return true;
        }

        try {
            $eliminados = Storage::disk('public')->delete($rutas);

            if (!$eliminados) {
                Log::warning('No se pudieron eliminar todos los archivos de una publicación.', [
                    'rutas' => $rutas,
                ]);
            }

            return $eliminados;

        } catch (\Throwable $error) {

            report($error);

            return false;
        }
    }

    private function redirigir(
        Request $request,
        Solicitud $solicitud,
        string $mensaje
    ): RedirectResponse {
        if ($request->input('origen') === 'perfil_profesional') {
            return redirect()
                ->route('profesional.show', [
                    'id' => $solicitud->profesional_id,
                    'tab' => 'trabajos',
                ])
                ->with('exito', $mensaje);
        }

        return redirect()
            ->route('solicitud.show', $solicitud->id)
            ->with('exito', $mensaje);
    }
}