<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
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
            if($usuario->tipo==='cliente'){
                return redirect()->route('profesional.listado')->with('nombre', 'hola '.$usuario->usuario); //para un usuario  
            }else{
                return redirect()->route('profesional.perfil', $usuario);// para un profesional
            }
        } else {
            return redirect()->route('profesional.perfil', $usuario->profesional->id); // para un profesional
        }
    }

    public function create(): View
    {
        return view('usuario.create');
    }


    // public function registrar(Request $request):RedirectResponse
    // {
    //     //aca iria la validacion de un nuevo usuario
    //     $validated = $request->validate([
    //         'usuario' => ['required', 'string', 'max:20'],
    //         'telefono' => ['required', 'string', 'max:12'],
    //         'email' => ['required', 'string', 'max:1000'],
    //         'tipo'=>['required','in:comun,profesional'],
    //         'imagen' => [
    //             'required',
    //             'image',
    //             Rule::file()->types(['jpg', 'jpeg', 'png', 'webp'])->max('2mb'),
    //         ],
    //         'password' => ['required', 'string', 'min:8', 'max:20'],
    //     ],[
    //         'usuario.required'=>'El usuario es obligatorio',
    //         'telefono.required'=>'El telefono es obligatorio',
    //         'email.required'=>'El email es obligatorio',
    //         'password.required'=>'La contraseña es obligatoria',
    //         'password.min'=>'La contraseña es muy corta',
    //     ]);

    //     if($validated['tipo']==='profesional'){
    //         User::create([
    //         'usuario' => $validated['usuario'],
    //         'telefono' => $validated['telefono'],
    //         'email' => $validated['email'],
    //         'password' => Hash::make($validated['password']),
    //         'imagen' => $request->file('imagen')->store('usuario', 'public'),
    //         'tipo' => $validated['tipo'],
    //         ]);   

    //     }else{
    //         User::create([
    //             'usuario' => $validated['usuario'],
    //             'telefono' => $validated['telefono'],
    //             'email' => $validated['email'],
    //             'password' => Hash::make($validated['password']),
    //             'imagen' => $request->file('imagen')->store('usuario', 'public'),
    //             'tipo' => $validated['tipo'],
    //         ]);
    //     }


    //     return redirect()->route('usuario.registro');
    // }


    //para que el usuario pueda cerrar sesion
    public function salir(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('usuario.registro');
    }
}