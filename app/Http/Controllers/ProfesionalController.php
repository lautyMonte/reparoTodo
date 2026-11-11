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
    {                                  //deben tener el nombre de mi funcion que hace la conexion entre usuarios y oficios de un profesional
        $profesionales=Profesional::with('user','oficios')->get();
        //$profesional=User::where('tipo','profesional')->orderBy('usuario')->get();

        return view('profesional.index',[
            'profesionales'=>$profesionales,
            'titulo' => 'Muestra',
            'modelo' => new Profesional()//User()
        ]);
    }
}
// public function index(): View
//     {
//         return view('peliculas.index', [
//             'peliculas' => Pelicula::withCount('resenas')
//                 ->orderBy('titulo')
//                 ->get(),
//             'titulo' => 'Catalogo de peliculas',
//             'activeFilter' => 'todas',
//             'modelo' => new Pelicula(),
//         ]);
//     }