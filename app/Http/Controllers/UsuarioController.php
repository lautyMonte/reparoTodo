<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            return redirect()->route('inicio')->with('exito', 'Hola, '.$usuario->usuario);
        } else {
            return back()->with('error', 'Usuario o contraseña incorrectos')->withInput();
        }
    }


    public function create(): View
    {
        return view('usuario.create');
    }


    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'usuario' => ['required', 'string', 'max:20', 'unique:users,usuario'],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['required', 'regex:/^\+?[0-9 ]{8,15}$/'],
            'password' => ['required', 'string', 'min:8', 'max:20', 'confirmed'],
            'terminos' => ['accepted'],
        ], [
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'El nombre de usuario ya está en uso.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'terminos.accepted' => 'Debés aceptar los términos y condiciones.',
        ]);

        User::create([
            'usuario' => $validated['usuario'],
            'nombre' => $validated['nombre'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('usuario.registro')
            ->with('exito', 'Cuenta creada correctamente. Ya podés iniciar sesión.');
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
