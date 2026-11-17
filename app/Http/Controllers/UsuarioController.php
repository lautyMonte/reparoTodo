<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
//para manejar la autenticacion de usuarios
use Illuminate\Support\Facades\Hash;//contraseña
use Illuminate\Support\Facades\Auth;//usuario logueado


class UsuarioController extends Controller
{

    public function registro(): View
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
            Auth::login($usuario);
            $request->session()->regenerate();
            if($usuario->tipo==='comun'){
                return redirect()->route('profesional.listado')->with('nombre', 'hola '.$usuario->usuario); //para un usuario  
            }else{
                return redirect()->route('profesional.perfil', $usuario);// para un profesional
            }
         }else{
             return redirect()->route('usuario.registro')->with('error','usuario o contraseña incorrecta');
         }
        
    }

    public function registrar(){
        //aca iria la validacion de un nuevo usuario
    }


    //para que el usuario pueda cerrar sesion
    public function salir(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('usuario.registro');
    }
}