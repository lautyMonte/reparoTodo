<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Oficio;
use App\Models\Profesional;
use App\Models\Jurisdiccion;
use App\Models\Localidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;

class ProfesionalController extends Controller
{

    public function __construct(

    ) {
    }

    /** Mostrar listado de todos los profesionales*/
     public function index(): View
    {                                  // Deben tener el nombre de la funcion que hace la conexion entre usuarios y oficios de un profesional
        $profesionales = Profesional::where('publicado', true)
            ->with('user', 'oficios')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->orderByDesc('resenas_avg_puntaje')
            ->orderByDesc('resenas_count')
            ->get();

        return view('profesional.index',[
            'profesionales'=>$profesionales,
            'titulo' => 'Buscar profesional',
            'modelo' => new Profesional()
        ]);
    }

    public function show(int $id): View
    {
       $profesional = Profesional::where('publicado', true)
            ->with('user', 'oficios', 'resenas.user')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->findOrFail($id);

        return view('profesional.show', [
            'profesional' => $profesional,
            'titulo' => $profesional->user->usuario,
            'modelo' => new Profesional(),
        ]);
    }


    public function miPerfil(Request $request): View
    {
        // Obtener el perfil del usuario autenticado.
        $profesional = $request->user()->profesional;

        // Si tiene perfil, cargar sus relaciones.
        if ($profesional) {
            $profesional->load([
                'oficios',
                'localidadPrincipal',
                'localidadesCobertura',
            ]);
        }

        // Recuperar las localidades seleccionadas.
        $idsCobertura = old(
            'localidades_cobertura',
            $profesional?->localidadesCobertura->modelKeys() ?? []
        );

        $idsCobertura = is_array($idsCobertura)
            ? $idsCobertura
            : [];

        return view('profesional.mi-perfil', [
            'titulo' => 'Mi perfil profesional',
            'profesional' => $profesional,

            // Opciones disponibles para los formularios.
            'oficiosDisponibles' => Oficio::orderBy('tipo')->get(),

            'jurisdicciones' => Jurisdiccion::orderBy('nombre')->get(),

            // Localidades que el profesional ya seleccionó.
            'localidadesSeleccionadas' => Localidad::with('jurisdiccion')
                ->whereIn('id', $idsCobertura)
                ->orderBy('nombre')
                ->get(),
        ]);
    }


    public function crearBorrador(Request $request): RedirectResponse
    {
        $profesional = $request->user()
            ->profesional()
            ->firstOrCreate([]);

        $mensaje = $profesional->wasRecentlyCreated
            ? 'Tu perfil profesional fue creado como borrador.'
            : 'Ya tenés un perfil profesional asociado a tu cuenta.';

        return redirect()
            ->route('profesional.mi-perfil')
            ->with('exito', $mensaje);
    }

    public function actualizarPerfil(Request $request): RedirectResponse
    {
        // Obtener exclusivamente el perfil del usuario autenticado.
        $profesional = $request->user()->profesional;

        abort_unless($profesional, 404);

        // Validar los datos enviados desde el formulario.
        $datos = $request->validate([
            'descripcion' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'nombre_comercial' => [
                'nullable',
                'string',
                'max:150',
            ],

            'telefono_comercial' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^\+?[0-9 ]{8,20}$/',
            ],

            'localidad_id' => [
                'nullable',
                'integer',
                'exists:localidades,id',
            ],

            'oficios' => [
                'sometimes',
                'array',
            ],

            'oficios.*' => [
                'integer',
                'distinct',
                'exists:oficios,id',
            ],

            'localidades_cobertura' => [
                'sometimes',
                'array',
            ],

            'localidades_cobertura.*' => [
                'integer',
                'distinct',
                'exists:localidades,id',
            ],
        ]);

        // Recuperar las asociaciones seleccionadas.
        // Si no se seleccionó ninguna, utilizar un array vacío.
        $oficios = $datos['oficios'] ?? [];
        $cobertura = $datos['localidades_cobertura'] ?? [];

        // Un perfil publicado no puede quedar incompleto.
        if (
            $profesional->publicado &&
            (
                blank($datos['descripcion'] ?? null) ||
                empty($datos['localidad_id']) ||
                count($oficios) === 0 ||
                count($cobertura) === 0
            )
        ) {
            throw ValidationException::withMessages([
                'perfil' => 'Un perfil publicado debe conservar su descripción, localidad principal, al menos un oficio y una localidad de cobertura.',
            ]);
        }

        // Guardar datos y asociaciones en una transacción.
        DB::transaction(function () use (
            $profesional,
            $datos,
            $oficios,
            $cobertura
        ) {
            $profesional->update([
                'descripcion' => $datos['descripcion'] ?? null,
                'nombre_comercial' => $datos['nombre_comercial'] ?? null,
                'telefono_comercial' => $datos['telefono_comercial'] ?? null,
                'localidad_id' => $datos['localidad_id'] ?? null,
            ]);

            // Sincronizar oficios ofrecidos.
            $profesional->oficios()->sync($oficios);

            // Sincronizar localidades de cobertura.
            $profesional->localidadesCobertura()->sync($cobertura);
        });

        return redirect()
            ->route('profesional.mi-perfil')
            ->with('exito', 'Los cambios del perfil se guardaron correctamente.');
    }

    /** Home: muestra los 3 profesionales con mejor promedio de resenas */
    public function destacados(): View {
        $destacados = Profesional::where('publicado', true)
            ->with('user', 'oficios')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->has('resenas')
            ->orderByDesc('resenas_avg_puntaje')
            ->orderByDesc('resenas_count')
            ->limit(3)
            ->get();

        return view('inicio', [
            'titulo' => 'Inicio',
            'destacados' => $destacados,
        ]);
    }
}