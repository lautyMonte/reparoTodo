<?php

namespace App\Http\Controllers;

use App\Models\Ciudad;
use App\Models\Oficio;
use App\Models\Profesional;
use App\Models\Provincia;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// Para manejar la autenticacion de usuarios
use Illuminate\Validation\ValidationException; // Contraseña
// Usuario logueado
use Illuminate\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

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
        $oficios = Oficio::orderBy('tipo')->get();

        $provincias = Provincia::with('ciudades')->orderBy('nombre')->get();

        return view('usuario.create', [
            'oficios' => $oficios,
            'provincias' => $provincias,
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
                'genero' => ['required', 'in:m,f']
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
                'genero.required' => 'Porfavor seleccione una opción'
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
                'genero' => $validated['genero']
            ]);

        } else {
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
                'especialidad' => ['required', 'array', 'min:1'],
                'especialidad.*' => ['required', 'integer', 'distinct', 'exists:oficios,id'],
                'provincia' => ['required', 'array', 'min:1'],
                'provincia.*' => ['required', 'integer', 'distinct', 'exists:provincias,id'],
                'ciudad' => ['required', 'array', 'min:1'],
                'ciudad.*' => ['required', 'integer', 'distinct', 'exists:ciudades,id'],                
                'genero' => ['required', 'in:m,f']

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
                'especialidad.required' => 'Debe seleccionar al menos una especialidad',
                'especialidad.array' => 'Las especialidades deben enviarse como un arreglo',
                'especialidad.min' => 'Debe seleccionar al menos una especialidad',
                'especialidad.*.required' => 'La especialidad es obligatoria',
                'especialidad.*.integer' => 'La especialidad seleccionada no es válida',
                'especialidad.*.distinct' => 'No puede seleccionar especialidades repetidas',
                'especialidad.*.exists' => 'La especialidad seleccionada no existe',
                'provincia.required' => 'Debe seleccionar al menos una provincia',
                'provincia.array' => 'Las provincias deben enviarse como un arreglo',
                'provincia.min' => 'Debe seleccionar al menos una provincia',
                'provincia.*.required' => 'La provincia es obligatoria',
                'provincia.*.integer' => 'La provincia seleccionada no es válida',
                'provincia.*.distinct' => 'No puede seleccionar provincias repetidas',
                'provincia.*.exists' => 'La provincia seleccionada no existe',
                'ciudad.required' => 'Debe seleccionar al menos una ciudad',
                'ciudad.array' => 'Las ciudades deben enviarse como un arreglo',
                'ciudad.min' => 'Debe seleccionar al menos una ciudad',
                'ciudad.*.required' => 'La ciudad es obligatoria',
                'ciudad.*.integer' => 'La ciudad seleccionada no es válida',
                'ciudad.*.distinct' => 'No puede seleccionar ciudades repetidas',
                'ciudad.*.exists' => 'La ciudad seleccionada no existe',
                'genero.required' => 'Porfavor seleccione una opción'
            ]);
            $ciudadesValidas = Ciudad::whereIn('id', $validated['ciudad'])
                ->whereIn('provincia_id', $validated['provincia'])
                ->count();

            if ($ciudadesValidas !== count($validated['ciudad'])) {
                throw ValidationException::withMessages([
                    'ciudad' => 'Las ciudades deben pertenecer a las provincias seleccionadas',
                ]);
            }

            DB::transaction(function () use ($validated) {

                $usuario = User::create([
                    'usuario' => $validated['usuario'],
                    'nombre' => $validated['nombre'],
                    'apellido' => $validated['apellido'],
                    'telefono' => $validated['telefono'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                    'imagen' => null,
                    'tipo' => 'profesional',
                    'genero' => $validated['genero']
                ]);

                // Crear el profesional asociado al usuario
                $profesional = $usuario->profesional()->create([]);

                // Asociar los oficios seleccionados
                $profesional->oficios()->attach($validated['especialidad']);

                // Asociar las ciudades seleccionadas
                $profesional->ciudades()->attach($validated['ciudad']);

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

    public function edit(Request $request)
    {
        $usuario = $request->user();

        Gate::authorize('update', $usuario);

        return view('perfil.edit', [
            'usuario' => $usuario,
            'profesional' => null,
            'oficios' => null,
            'provincias'=>null, 
        ]);
    }

    public function update(Request $request)
    {
        $usuario = $request->user();

        Gate::authorize('update', $usuario);

        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:50'],
            'apellido' => ['required', 'string', 'max:50'],
            'telefono' => ['required', 'string', 'max:16'],
            'email' => ['required','email',
                    Rule::unique('users', 'email')->ignore($usuario->id),
            ],
            'imagen' => ['nullable','image',Rule::file()->types(['jpg', 'jpeg', 'png'])->max('2mb')],
            'portada' => ['nullable','image',Rule::file()->types(['jpg', 'jpeg', 'png', 'webp'])->max('2mb')],
        ], [
            'nombre.required' => 'El nombre es obligatorio',
            'apellido.required' => 'El apellido es obligatorio',
            'telefono.required' => 'El teléfono es obligatorio',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'Ingrese un email válido',
            'email.unique' => 'Ese email ya está registrado',
            'imagen.image' => 'La foto de perfil debe ser una imagen',
            'imagen.max' => 'La foto de perfil no puede pesar más de 2 MB',
            'portada.image' => 'La portada debe ser una imagen',
            'portada.max' => 'La portada no puede pesar más de 2 MB',
        ]);

         // Guardar rutas de imágenes anteriores
         $archivosAnteriores = [];

        
            
            $datos = [ 'nombre'=> $validated['nombre'],
                  'apellido'=> $validated['apellido'],
                  'telefono'=> $validated['telefono'] ?? null,
                  'email' => $validated['email']];

            if ($request->hasFile('imagen')) {
            if ($usuario->imagen) {
                $archivosAnteriores[] = $usuario->imagen;
            }
            $datos['imagen'] = $request->file('imagen')
                ->store('perfil', 'public');
        }

        if ($request->hasFile('portada')) {
            if ($usuario->portada) {
                $archivosAnteriores[] = $usuario->portada;
            }
            $datos['portada'] = $request->file('portada')
                ->store('portada', 'public');
        }
         $usuario->update($datos);
        

        //Eliminar imágenes anteriores después de confirmar la transacción
        foreach (array_unique($archivosAnteriores) as $archivo) {
            Storage::disk('public')->delete($archivo);
        }

        return redirect()->route('usuario.edit')->with('exito', 'Perfil actualizado correctamente.');
    }
}
