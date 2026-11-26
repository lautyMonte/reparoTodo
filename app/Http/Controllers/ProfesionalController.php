<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Oficio;
use App\Models\Profesional;
use App\Models\Provincia;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfesionalController extends Controller
{
    /** Listado de profesionales del buscador (con los oficios elegidos ya marcados) */
    public function index(Request $request)
    {
        // Valida ids recibidos por URL
        $datos = $request->validate([
            'oficios' => ['sometimes', 'array'],
            'oficios.*' => ['integer', 'exists:oficios,id'],
        ]);

        $oficiosSeleccionados = $datos['oficios'] ?? [];

        $profesionales = Profesional::with('user', 'oficios', 'ciudades.provincia')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->when($oficiosSeleccionados, function ($query) use ($oficiosSeleccionados) {
                $query->whereHas('oficios', function ($q) use ($oficiosSeleccionados) {
                    $q->whereIn('oficios.id', $oficiosSeleccionados);
                });
            })
            ->orderByDesc('resenas_avg_puntaje')
            ->orderByDesc('resenas_count')
            ->get();

        $oficios = Oficio::orderBy('tipo')->get();

        // Obtener las ciudades que tienen profesionales asociados
        $localidades = Ciudad::whereHas('profesionales')
            ->distinct()
            ->orderBy('nombre')
            ->pluck('nombre');

        return view('profesional.index', compact(
            'profesionales',
            'oficios',
            'localidades',
            'oficiosSeleccionados'
        ) + [
            'titulo' => 'Buscar profesional',
        ]);
    }

    public function show(Request $request, int $id): View
    {
        $profesional = Profesional::with(
            'user',
            'oficios',
            'resenas.user',
            'ciudades.provincia'
        )
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->findOrFail($id);

        return view('profesional.show', [
            'profesional' => $profesional,
            'titulo' => $profesional->user->nombreCompleto(),
            'modelo' => new Profesional,

            // Cuantas resenas hay de cada puntaje (para las barras)
            'conteo' => $profesional->resenas->countBy('puntaje'),

            // Si quien esta mirando es el dueño del perfil
            'esDuenio' => $request->user() !== null
                && $request->user()->id === $profesional->user_id,
        ]);
    }

    /** Home: muestra los 3 profesionales con mejor promedio de resenas */
    public function destacados(): View
    {
        $destacados = Profesional::with('user', 'oficios', 'ciudades.provincia')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->has('resenas')
            ->orderByDesc('resenas_avg_puntaje')
            ->orderByDesc('resenas_count')
            ->limit(3)
            ->get();

        return view('inicio', [
            'titulo' => 'Inicio',
            'destacados' => $destacados,
        ]);
    }

    /**
     * Buscara a los profesionales por su nombre, oficio, localidad o puntuacion
     * (tambien funciona si es todo junto)
     */
    public function filtrar(Request $request)
    {
        $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],

            'oficios' => ['nullable', 'array'],
            'oficios.*' => ['integer', 'exists:oficios,id'],

            'localidades' => ['nullable', 'array'],
            'localidades.*' => ['string', 'max:100'],

            'puntajes' => ['nullable', 'array'],
            'puntajes.*' => ['integer', 'between:1,5'],
        ]);

        $buscar = trim($request->input('buscar', ''));
        $oficios = $request->input('oficios', []);
        $localidades = $request->input('localidades', []);
        $puntajes = $request->input('puntajes', []);

        $profesionales = Profesional::with([
            'user',
            'oficios',
            'ciudades.provincia',
        ])
            ->withCount('resenas')
            ->withAvg('resenas', 'puntaje')

            // Buscar por nombre
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->whereHas('user', function ($q) use ($buscar) {
                    // Cada palabra escrita tiene que estar en el nombre o en el apellido
                    foreach (explode(' ', $buscar) as $palabra) {
                        $q->where(function ($subconsulta) use ($palabra) {
                            $subconsulta->where('nombre', 'LIKE', "%{$palabra}%")
                                ->orWhere('apellido', 'LIKE', "%{$palabra}%");
                        });
                    }
                });
            })

            // Filtrar por oficio
            ->when(! empty($oficios), function ($query) use ($oficios) {

                $query->whereHas('oficios', function ($q) use ($oficios) {
                    $q->whereIn('oficios.id', $oficios);
                });

            })

            // Filtrar por localidad
            ->when(! empty($localidades), function ($query) use ($localidades) {

                $query->whereHas('ciudades', function ($q) use ($localidades) {
                    $q->whereIn('ciudades.nombre', $localidades);
                });

            })

            ->get();

        // Filtrar por puntuación minima
        if (! empty($puntajes)) {

            $puntajeMinimo = min($puntajes);

            $profesionales = $profesionales->filter(function ($profesional) use ($puntajeMinimo) {

                return $profesional->resenas_avg_puntaje !== null
                    && $profesional->resenas_avg_puntaje >= $puntajeMinimo;

            })->values();

        }

        return response()->json([
            'html' => view(
                'partials.tarjetas',
                compact('profesionales')
            )->render(),
        ]);
    }
    
     public function edit(Request $request)
    {
        $usuario = $request->user();

        $profesional = $usuario->profesional()->with('oficios', 'ciudades')->firstOrFail();

        Gate::authorize('update', $profesional);

        $oficios = Oficio::all();

        $provincias = Provincia::with('ciudades')->orderBy('nombre')->get();

        return view('perfil.edit', compact('usuario', 'profesional', 'oficios', 'provincias'));
    }

    public function update(Request $request)
    {
        $usuario = $request->user();

        $profesional = $usuario->profesional()->firstOrFail();

        Gate::authorize('update', $profesional);

        $validated = $request->validate([

        // Datos del usuario
        'usuario' => ['required','string','max:20',
                    Rule::unique('users', 'usuario')->ignore($usuario->id),
            ],
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'telefono' => ['required', 'string', 'max:16'],
            'email' => ['required','email',
                    Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'imagen' => ['nullable','image',Rule::file()->types(['jpg', 'jpeg', 'png', 'webp'])->max('2mb')],

        // Ciudades donde trabaja
        'ciudades' => ['required', 'array', 'min:1'],

        'ciudades.*' => ['required', 'integer', 'distinct', 'exists:ciudades,id'],

        // Oficios
        'oficios' => ['required', 'array', 'min:1'],

        'oficios.*' => ['required','integer','distinct','exists:oficios,id'],
        ]);

        DB::transaction(function () use ($request, $usuario,$profesional,$validated) 
        {

        // Datos del usuario
        $datosUsuario = ['usuario' => $validated['usuario'],
                  'nombre'=> $validated['nombre'],
                  'apellido'=> $validated['apellido'],
                  'telefono'=> $validated['telefono'] ?? null,
                  'email' => $validated['email']
        ];

        if ($request->hasFile('imagen')) {
            $datosUsuario['imagen'] = $request->file('imagen')->store('perfil', 'public');
        }
        

        $usuario->update($datosUsuario);

        // Actualizar ciudades
        $profesional->ciudades()->sync($validated['ciudades']);

        // Actualizar oficios
        $profesional->oficios()->sync($validated['oficios']);
    });

    return redirect()->route('profesional.edit')->with('success', 'Perfil profesional actualizado correctamente.');
    }
}

