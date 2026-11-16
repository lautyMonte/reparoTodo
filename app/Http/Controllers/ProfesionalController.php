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

        return view('profesional.listado',[
            'profesionales'=>$profesionales,
            'titulo' => 'Muestra',
            'modelo' => new Profesional()
        ]);
    }

    public function perfil(int $id): View
    {
       $profesional=Profesional::with('user','oficios')->find($id);

        return view('profesional.perfil', [
            'profesional' => $profesional,
            'titulo'=> 'el profesional',
            'modelo' => new Profesional(),
        ]);
    }
}