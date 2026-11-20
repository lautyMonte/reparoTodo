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
       $profesional = Profesional::with('user', 'oficios')->findOrFail($id);

        return view('profesional.show', [
            'profesional' => $profesional,
            'titulo'=> 'el profesional',
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
}