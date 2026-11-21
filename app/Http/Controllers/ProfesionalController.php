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
     public function index(): View
    {                                  // Deben tener el nombre de la funcion que hace la conexion entre usuarios y oficios de un profesional
        $profesionales = Profesional::with('user', 'oficios')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->orderByDesc('resenas_avg_puntaje')
            ->orderByDesc('resenas_count')
            ->get();

        return view('profesional.index',[
            'profesionales'=>$profesionales,
            'titulo' => 'Buscar profesional',
            'modelo' => new Profesional()
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

    // public function buscar(Request $request)
    // {
    // $request->validate([
    //     'buscar' => ['nullable', 'string', 'max:100'],
    // ]);

    // $buscar = trim($request->input('buscar', ''));

    // $profesionales = Profesional::with(['user', 'oficios'])
    //     ->withCount('resenas')
    //     ->withAvg('resenas', 'puntaje')
    //     ->when($buscar, function ($query) use ($buscar) {
    //         $query->whereHas('user', function ($q) use ($buscar) {
    //             $q->where('usuario', 'LIKE', "%{$buscar}%");
    //         });
    //     })
    //     ->get();

    // return response()->json([
    //     'html' => view('partials.tarjetas', compact('profesionales'))->render(),
    // ]);
    // }

    public function buscar(Request $request)
    {
    $request->validate([
        'buscar' => ['nullable', 'string', 'max:100'],
    ]);

     $buscar = $request->input('buscar');

    $profesionales = Profesional::with(['user', 'oficios'])
        ->withCount('resenas')
        ->withAvg('resenas', 'puntaje')
        ->whereHas('user', function ($query) use ($buscar) {
            $query->where('usuario', 'LIKE', "%{$buscar}%");
        })
        ->get();

    return view('profesional.index', [
        'titulo' => 'Buscar profesional',
        'profesionales' => $profesionales
    ]);
    }
}