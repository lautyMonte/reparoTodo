<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Oficio;
use App\Models\Profesional;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class ProfesionalController extends Controller
{

    public function __construct(
        
    ) {
    }

    /** Mostrar listado de todos los profesionales*/
    //  public function index(): View
    // {                                  // Deben tener el nombre de la funcion que hace la conexion entre usuarios y oficios de un profesional
    //     $profesionales = Profesional::with('user', 'oficios')
    //         ->withAvg('resenas', 'puntaje')
    //         ->withCount('resenas')
    //         ->orderByDesc('resenas_avg_puntaje')
    //         ->orderByDesc('resenas_count')
    //         ->get();

    //     return view('profesional.index',[
    //         'profesionales'=>$profesionales,
    //         'titulo' => 'Buscar profesional',
    //         'modelo' => new Profesional()
    //     ]);
    // }
    public function index(Request $request)
    {

    //valida ids recibidos por URL
    $datos = $request->validate([
        'oficios' => ['sometimes', 'array'],
        'oficios.*' => ['integer', 'exists:oficios,id'],
    ]);

    $oficiosSeleccionados = $datos['oficios'] ?? [];

    $profesionales = Profesional::with('user', 'oficios')
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

    $localidades = Profesional::whereNotNull('ciudad')
        ->where('ciudad', '!=', '')
        ->distinct()
        ->orderBy('ciudad')
        ->pluck('ciudad');

    return view('profesional.index', compact(
        'profesionales',
        'oficios',
        'localidades',
        'oficiosSeleccionados'
    ) + [
        'titulo' => 'Buscar profesional'
    ]);
    }

    public function show(int $id): View
    {
       $profesional = Profesional::with('user', 'oficios', 'resenas.user')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->findOrFail($id);

        return view('profesional.show', [
            'profesional' => $profesional,
            'titulo' => $profesional->user->usuario,
            'modelo' => new Profesional(),
        ]);
    }

    /** Home: muestra los 3 profesionales con mejor promedio de resenas */
    public function destacados(): View {
        $destacados = Profesional::with('user', 'oficios')
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

    $profesionales = Profesional::with(['user', 'oficios'])
        ->withCount('resenas')
        ->withAvg('resenas', 'puntaje')

        // Buscar por nombre
        ->when($buscar !== '', function ($query) use ($buscar) {
            $query->whereHas('user', function ($q) use ($buscar) {
                $q->where('usuario', 'LIKE', "%{$buscar}%");
            });
        })

        // Filtrar por oficio
        ->when(!empty($oficios), function ($query) use ($oficios) {

            $query->whereHas('oficios', function ($q) use ($oficios) {
                $q->whereIn('oficios.id', $oficios);
            });

        })

        // Filtrar por localidad
        ->when(!empty($localidades), function ($query) use ($localidades) {

            $query->whereIn('ciudad', $localidades);

        })

        ->get();

    // Filtrar por puntuación minima
    if (!empty($puntajes)) {

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
        )->render()
    ]);
}
}