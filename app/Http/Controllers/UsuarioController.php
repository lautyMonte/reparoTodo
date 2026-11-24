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
use Illuminate\Validation\Rule; // usuario logueado
use Illuminate\View\View;

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

            return redirect()->route('inicio')->with('exito', 'Hola, '.$usuario->nombreCompleto());
        } else {
            return back()->with('error', 'Usuario o contraseña incorrectos')->withInput();
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
        // Validar el tipo de cuenta y los términos y condiciones
        $request->validate([
            'tipo' => ['required', 'in:cliente,profesional'],
            'terminos' => ['required', 'accepted'],
        ], [
            'tipo.required' => 'Debe seleccionar un tipo de cuenta',
            'tipo.in' => 'El tipo de cuenta no es válido',
            'terminos.required' => 'Debe aceptar los términos y condiciones',
            'terminos.accepted' => 'Debe aceptar los términos y condiciones',
        ]);

        if ($request->tipo === 'cliente') {
            $validated = $request->validate([
                'usuario' => ['required', 'string', 'max:20', 'unique:users,usuario'],
                'nombre' => ['required', 'string', 'max:50'],
                'apellido' => ['required', 'string', 'max:50'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'max:20'],
                'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'same:password'],
                'telefono' => ['required', 'regex:/^\+?[0-9 ]{8,15}$/'],
                'imagen' => ['nullable', 'image'],
            ], [
                'usuario.required' => 'El usuario es obligatorio',
                'usuario.unique' => 'El nombre de usuario ya está registrado',
                'usuario.max' => 'El nombre de usuario es demasiado largo',
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.max' => 'El nombre es demasiado largo',
                'apellido.required' => 'El apellido es obligatorio',
                'apellido.max' => 'El apellido es demasiado largo',
                'telefono.required' => 'El teléfono es obligatorio',
                'telefono.regex' => 'Ingrese un número de teléfono válido',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'Ingrese un correo electrónico válido',
                'email.unique' => 'El correo electrónico ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña es demasiado corta',
                'password.max' => 'La contraseña es demasiado larga',
                'password_confirmation.required' => 'Debe confirmar su contraseña',
                'password_confirmation.same' => 'Las contraseñas no coinciden',
            ]);

            User::create([
                'usuario' => $validated['usuario'],
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'],
                'password' => Hash::make($validated['password']),
                'imagen' => null,
                'tipo' => 'cliente',
            ]);

        } else {
            $ciudades = [
                'nqn' => ['Centenario', 'Neuquén', 'Plottier', 'San Martín de los Andes'],
                'rn' => ['Bariloche', 'Cinco Saltos', 'Cipolletti', 'General Roca'],
            ];

            $provinciaSeleccionada = $request->input('provincia');

            $ciudadesPermitidas = is_string($provinciaSeleccionada) ? ($ciudades[$provinciaSeleccionada] ?? []) : [];
            
            // Para registrar un profesional
            $validated = $request->validate([
                'usuario' => ['required', 'string', 'max:20', 'unique:users,usuario'],
                'nombre' => ['required', 'string', 'max:50'],
                'apellido' => ['required', 'string', 'max:50'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'telefono' => ['required', 'regex:/^\+?[0-9 ]{8,15}$/'],
                'password' => ['required', 'string', 'min:8', 'max:20'],
                'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'same:password'],
                'imagen' => ['nullable', 'image'],
                'especialidad' => ['required', 'exists:oficios,id'],
                'provincia' => ['required', 'in:nqn,rn'],
                'ciudad' => ['required', 'string', 'max:100',
                    Rule::in($ciudades[$request->input('provincia')] ?? []),
                ],

            ], [
                'usuario.required' => 'El usuario es obligatorio',
                'usuario.unique' => 'El nombre de usuario ya está registrado',
                'usuario.max' => 'El nombre de usuario es demasiado largo',
                'nombre.required' => 'El nombre es obligatorio',
                'nombre.max' => 'El nombre es demasiado largo',
                'apellido.required' => 'El apellido es obligatorio',
                'apellido.max' => 'El apellido es demasiado largo',
                'telefono.required' => 'El teléfono es obligatorio',
                'telefono.regex' => 'Ingrese un número de teléfono válido',
                'email.required' => 'El email es obligatorio',
                'email.email' => 'Ingrese un correo electrónico válido',
                'email.unique' => 'El correo electrónico ya está registrado',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña es demasiado corta',
                'password.max' => 'La contraseña es demasiado larga',
                'password_confirmation.required' => 'Debe confirmar su contraseña',
                'password_confirmation.same' => 'Las contraseñas no coinciden',
                'especialidad.required' => 'La especialidad es obligatoria',
                'provincia.required' => 'La provincia es obligatoria',
                'ciudad.required' => 'La ciudad es obligatoria',
                'ciudad.in' => 'La ciudad no pertenece a la provincia seleccionada',
            ]);
            $provincias = ['nqn' => 'Neuquén', 'rn' => 'Río Negro'];

            DB::transaction(function () use ($validated, $provincias) {

                $usuario = User::create([
                    'usuario' => $validated['usuario'],
                    'nombre' => $validated['nombre'],
                    'apellido' => $validated['apellido'],
                    'telefono' => $validated['telefono'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'imagen' => null,
                    'tipo' => 'profesional',
                ]);

                $profesional = $usuario->profesional()->create([
                    'provincia' => $provincias[$validated['provincia']],
                    'ciudad' => $validated['ciudad'],
                ]);

                $profesional->oficios()->attach($validated['especialidad']);
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
