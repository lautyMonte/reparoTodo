<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Profesional;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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
         }else{
             return redirect()->route('usuario.registro')->with('error','usuario o contraseña incorrecta');
         }
        
    }

    public function create(): View
    {
        return view('usuario.create');
    }


    public function store(Request $request):RedirectResponse
    {
        //aca iria la validacion de un nuevo usuario
        if($request->tipo==='cliente'){
            $validated = $request->validate([
            'usuario' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'same:password'],
            'imagen' => [
                'required',
                'image',
                Rule::file()->types(['jpg', 'jpeg', 'png', 'webp'])->max('2mb'),
            ],
        ],[
            'usuario.required'=>'El usuario es obligatorio',
            'telefono.required'=>'El telefono es obligatorio',
            'email.required'=>'El email es obligatorio',
            'password.required'=>'La contraseña es obligatoria',
            'password.min'=>'La contraseña es muy corta',
        ]);

            User::create([
            'usuario' => $validated['usuario'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
            'imagen' => $request->file('imagen')->store('usuario', 'public'),
            'tipo' => 'cliente',
            ]);   

        }else{
                 //para ingresar un profesional
            $validated = $request->validate([
            'usuario' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'max:1000'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'same:password'],
            'imagen' => [
                'required',
                'image',
                Rule::file()->types(['jpg', 'jpeg', 'png', 'webp'])->max('2mb'),
            ],
            'especialidad' => ['required', 'string', 'max:100'],
            'provincia' => ['required', 'string', 'max:100'],
            'ciudad' => ['required', 'string', 'max:100'],

        ],[
            'usuario.required'=>'El usuario es obligatorio',
            'telefono.required'=>'El telefono es obligatorio',
            'email.required'=>'El email es obligatorio',
            'password.required'=>'La contraseña es obligatoria',
            'password.min'=>'La contraseña es muy corta',
            'especialidad.required'=>'La especialidad es obligatoria',
            'provincia.required'=>'La provincia es obligatoria',
            'ciudad.required'=>'La ciudad es obligatoria',
        ]);
        DB::transaction(function () use ($validated, $request) {// esto es para que si falla una de las dos inserciones, no se haga ninguna

            $usuario=User::create([
                'usuario' => $validated['usuario'],
                'telefono' => $validated['telefono'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'imagen' => $request->file('imagen')->store('usuario', 'public'),
                'tipo' => 'profesional'
            ]);

             $usuario->profesional()->create([
                'especialidad' => $validated['especialidad'],
                'provincia' => $validated['provincia'],
                'ciudad' => $validated['ciudad'],
            ]);
            });
        }


        return redirect()->route('usuario.registro');
    }


    //para que el usuario pueda cerrar sesion
    public function salir(Request $request){
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('usuario.registro');
    }
}