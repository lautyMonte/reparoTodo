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
    public function index()
    {
    $profesionales = Profesional::with('user', 'oficios')
             ->withAvg('resenas', 'puntaje')
             ->withCount('resenas')
             ->orderByDesc('resenas_avg_puntaje')
             ->orderByDesc('resenas_count')
             ->get();

    $oficios = Oficio::orderBy('tipo')->get();

    return view('profesional.index', compact(
        'profesionales',
        'oficios'
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
     * Busca a los profesionales, según el nombre
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'buscar' => ['nullable', 'string', 'max:100'],
        ]);

        $buscar = trim($request->input('buscar', ''));

        $profesionales = Profesional::with(['user', 'oficios'])
            ->withCount('resenas')
            ->withAvg('resenas', 'puntaje')
            ->when($buscar, function ($query) use ($buscar) {
                $query->whereHas('user', function ($q) use ($buscar) {
                    $q->where('usuario', 'LIKE', "%{$buscar}%");
                });
            })
            ->get();

        return response()->json([
            'html' => view('partials.tarjetas', compact('profesionales'))->render(),
        ]);
    }

    /**
     * Segun el oficio
     */
    public function filtrarOficio(Request $request){
    
        $request->validate([
            'oficio' => ['required', 'integer', 'exists:oficios,id'],
        ]);

        $oficio = $request->input('oficio');

        $profesionales = Profesional::with(['user', 'oficios'])
            ->withCount('resenas')
            ->withAvg('resenas', 'puntaje')
            ->whereHas('oficios', function ($query) use ($oficio) {
                $query->where('oficios.id', $oficio);
            })
            ->get();

        return response()->json([
            'html' => view('partials.tarjetas', compact('profesionales'))->render()
        ]);
    }
}