<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{

    public function index(): View
    {
        return view('usuario.index',[
            'titulo' => 'Inicio'
        ]);
    }

     public function verifica(Request $request):RedirectResponse
    {
        
        $datos=$request->validate([
            'usuario'=>['required', 'string', 'max:20'],
            'password'=>['required', 'string', 'min:8','max:20'],

        ],[
            'usuario.required'=>'Escribir un usuario',
            'password.required'=>'Escribir una contraseña',
            'password.min'=>'contraseña muy corta',
        ]);
        
        $usuario = User::where('usuario', $datos['usuario'])->first();

        if ($usuario && Hash::check($datos['password'], $usuario->password)) {
            return redirect()->route('profesional.index');   
         }else{
             return redirect()->route('usuario.index')->with('error','usuario o contraseña incorrecta');
         }
        
    }

    /** Guardar un usuario nuevo*/
    // public function store(Request $request): JsonResponse|RedirectResponse
    // {
    //     $datos = $request->validate([
    //         '' => ['required', 'string', 'max:80'],
    //         '' => ['required', 'string', 'max:2000'],
    //         '' => ['required', 'integer', 'between:1,5'],
    //     ]);

    //     return redirect()->route('profesional.show');
    // }
}