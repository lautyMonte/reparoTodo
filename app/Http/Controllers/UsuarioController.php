<?php

namespace App\Http\Controllers;

use App\Models\Oficio;
use App\Models\Profesional;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
// para manejar la autenticacion de usuarios
use Illuminate\Support\Facades\Hash; // contraseña
use Illuminate\View\View; // usuario logueado

class UsuarioController extends Controller
{
    public function registro(): View
    {
        return view('usuario.index', [
            'titulo' => 'Inicio',
        ]);
    }

    public function verifica(Request $request): RedirectResponse
    {

        $datos = $request->validate([
            'usuario' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'max:20'],

        ], [
            'usuario.required' => 'Ingrese un usuario',
            'password.required' => 'Ingrese una contraseña',
            'password.min' => 'La contraseña debe tener un mínimo de 8 caracteres',
        ]);

        $usuario = User::where('usuario', $datos['usuario'])->first();

        if ($usuario && Hash::check($datos['password'], $usuario->password)) {
            Auth::login($usuario);
            $request->session()->regenerate();
            if ($usuario->tipo === 'cliente') {
                return redirect()->route('profesional.listado'); // para un usuario
            } else {
                return redirect()->route('profesional.perfil', $usuario); // para un profesional
            }
        } else {
            return redirect()->route('usuario.registro')->with('error', 'El usuario no existe'); // Devuelve al login
        }
    }

    public function create(): View
    {
        // return view('usuario.create');
        $oficios = Oficio::orderBy('tipo')->get();

        return view('usuario.create', [
            'oficios' => $oficios,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // aca iria la validacion de un nuevo usuario
        // para saber si el usuario que se creo ya existe
        $usuario = User::where('usuario', $request['usuario'])->first();
        if ($usuario) {
            return redirect()->route('usuario.create')->with('error', 'usuario ya existente');
        }
        if ($request->tipo === 'cliente') {
            $validated = $request->validate([
                'usuario' => ['required', 'string', 'max:20'],
                'email' => ['required', 'string', 'max:1000'],
                'password' => ['required', 'string', 'min:8', 'max:20'],
                'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'same:password'],
                'telefono' => ['required', 'regex:/^\+?[0-9 ]{8,15}$/'],
                'imagen' => ['nullable', 'image'],
            ], [
                'usuario.required' => 'El usuario es obligatorio',
                'telefono.required' => 'El telefono es obligatorio',
                'email.required' => 'El email es obligatorio',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña es muy corta',
                'password.max' => 'La contraseña es muy larga',
            ]);

            User::create([
                'usuario' => $validated['usuario'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'password' => Hash::make($validated['password']),
                'imagen' => null,
                'tipo' => 'cliente',
            ]);

        } else {
            // para ingresar un profesional
            $validated = $request->validate([
                'usuario' => ['required', 'string', 'max:20'],
                'email' => ['required', 'string', 'max:1000'],
                'telefono' => ['nullable', 'regex:/^\+?[0-9 ]{8,15}$/'],
                'password' => ['required', 'string', 'min:8', 'max:20'],
                'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'same:password'],
                'imagen' => ['nullable', 'image'],
                'especialidad' => ['required', 'string', 'max:100'],
                'provincia' => ['required', 'string', 'max:100'],
                'ciudad' => ['required', 'string', 'max:100'],

            ], [
                'usuario.required' => 'El usuario es obligatorio',
                'telefono.required' => 'El telefono es obligatorio',
                'email.required' => 'El email es obligatorio',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña es muy corta',
                'especialidad.required' => 'La especialidad es obligatoria',
                'provincia.required' => 'La provincia es obligatoria',
                'ciudad.required' => 'La ciudad es obligatoria',
            ]);
            DB::transaction(function () use ($validated) {// esto es para que si falla una de las dos inserciones, no se haga ninguna

                $usuario = User::create([
                    'usuario' => $validated['usuario'],
                    'telefono' => null,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'imagen' => null,
                    'tipo' => 'profesional',
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

    // para que el usuario pueda cerrar sesion
    public function salir(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('usuario.registro');
    }
}
