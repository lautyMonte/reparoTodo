<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Profesional;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SolicitudController extends Controller
{
    /** Muestra el formulario para pedirle un trabajo a un profesional */
    public function create(int $id): View
    {
        $profesional = Profesional::with('user')
            ->withAvg('resenas', 'puntaje')
            ->withCount('resenas')
            ->findOrFail($id);

        return view('solicitud.create', [
            'profesional' => $profesional,
            'titulo' => 'Solicitar trabajo',
        ]);
    }

    /** Valida y guarda la solicitud con sus fotos */
    public function store(Request $request, int $id): RedirectResponse
    {
        $profesional = Profesional::findOrFail($id);

        $datos = $request->validate([
            'titulo' => ['required', 'string', 'max:80'],
            'descripcion' => ['required', 'string', 'max:1000'],
            'zona' => ['nullable', 'string', 'max:100'],
            'fecha_deseada' => ['nullable', 'date', 'after_or_equal:today'],
            'fotos' => ['nullable', 'array', 'max:3'],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'titulo.required' => 'El nombre del trabajo es obligatorio',
            'titulo.max' => 'El nombre del trabajo puede tener hasta 80 caracteres',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.max' => 'La descripción puede tener hasta 1000 caracteres',
            'zona.max' => 'La zona puede tener hasta 100 caracteres',
            'fecha_deseada.date' => 'La fecha deseada no es válida',
            'fecha_deseada.after_or_equal' => 'La fecha deseada no puede ser anterior a hoy',
            'fotos.max' => 'Podés subir hasta 3 fotos',
            'fotos.*.image' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
            'fotos.*.mimes' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
            'fotos.*.max' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
            'fotos.*.uploaded' => 'Las fotos deben ser JPG, PNG o WEBP y pesar hasta 2 MB',
        ]);

        // El cliente sale de la sesion (no del formulario) para que nadie pueda pedir un trabajo a nombre de otro
        $solicitud = Solicitud::create([
            'cliente_id' => $request->user()->id,
            'profesional_id' => $profesional->id,
            'titulo' => $datos['titulo'],
            'descripcion' => $datos['descripcion'],
            'zona' => $datos['zona'] ?? null,
            'fecha_deseada' => $datos['fecha_deseada'] ?? null,
        ]);

        // Cada foto se guarda en storage/app/public/solicitudes y en la base queda solo su ruta
        foreach ($request->file('fotos', []) as $foto) {
            $solicitud->fotos()->create([
                'ruta' => $foto->store('solicitudes', 'public'),
            ]);
        }

        // Le avisamos al profesional que tiene una solicitud nueva (despues se va a ver en su campana)
        Notificacion::create([
            'user_id' => $profesional->user_id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'solicitud_nueva',
            'mensaje' => $request->user()->usuario.' te pidió «'.$solicitud->titulo.'»',
        ]);

        return redirect()
            ->route('profesional.show', $profesional->id)
            ->with('exito', 'Solicitud enviada. '.$profesional->user->usuario.' la va a revisar y te va a responder.');
    }

    /** Bandeja del profesional (se abre la solicitud mas nueva) */
    public function index(Request $request): View|RedirectResponse
    {
        return $this->bandeja($request, null);
    }

    /** Bandeja del profesional con una solicitud puntual abierta */
    public function show(Request $request, int $id): View|RedirectResponse
    {
        return $this->bandeja($request, $id);
    }

    /** Arma la bandeja: la lista de solicitudes y el detalle de la elegida */
    private function bandeja(Request $request, ?int $id): View|RedirectResponse
    {
        $profesional = $request->user()->profesional;

        // Las rechazadas no se muestran: para el profesional es como si se hubieran borrado
        $todas = $profesional->solicitudes()
            ->where('estado', '!=', 'rechazada')
            ->with(['cliente', 'fotos', 'presupuesto'])
            ->latest()
            ->get();

        // Si no pidio ninguna en particular, se abre la mas nueva
        $seleccionada = $id === null ? $todas->first() : $todas->firstWhere('id', $id);

        // Si pidio una que no es suya o que ya no esta, vuelve a la bandeja con un aviso
        if ($id !== null && $seleccionada === null) {
            return redirect()
                ->route('solicitud.index')
                ->with('error', 'Esa solicitud no existe o ya no está disponible.');
        }

        // Al abrirla queda leida, ella y sus notificaciones
        if ($seleccionada !== null) {
            if (! $seleccionada->leida_profesional) {
                $seleccionada->update(['leida_profesional' => true]);
            }

            Notificacion::where('user_id', $request->user()->id)
                ->where('solicitud_id', $seleccionada->id)
                ->update(['leida' => true]);
        }

        // Filtro de la lista (?estado=pendiente). Si viene cualquier otra cosa, se muestran todas
        $estado = $request->query('estado');
        if (! in_array($estado, ['pendiente', 'aceptada', 'presupuestada', 'en_chat'], true)) {
            $estado = null;
        }
        $lista = $estado === null ? $todas : $todas->where('estado', $estado);
        
        return view('solicitud.index', [
            'titulo' => 'Mi bandeja',
            'lista' => $lista,
            'seleccionada' => $seleccionada,
            'estado' => $estado,
            'conteo' => $todas->countBy('estado'),
            'total' => $todas->count(),
        ]);
    }

    /** Bandeja del cliente (se abre la solicitud mas nueva) */
    public function indexCliente(Request $request): View|RedirectResponse
    {
        return $this->bandejaCliente($request, null);
    }

    /** Bandeja del cliente con una solicitud puntual abierta */
    public function showCliente(Request $request, int $id): View|RedirectResponse
    {
        return $this->bandejaCliente($request, $id);
    }

    /** Arma la bandeja del cliente: la lista de sus solicitudes y el detalle de la elegida */
    private function bandejaCliente(Request $request, ?int $id): View|RedirectResponse
    {
        // Las rechazadas no se muestran: para el cliente tambien es como si se hubieran borrado
        $todas = $request->user()->solicitudes()
            ->where('estado', '!=', 'rechazada')
            ->with(['profesional.user', 'presupuesto', 'fotos'])
            ->latest()
            ->get();

        // Si no pidio ninguna en particular, se abre la mas nueva
        $seleccionada = $id === null ? $todas->first() : $todas->firstWhere('id', $id);

        // Si pidio una que no es suya o que ya no esta, vuelve a la bandeja con un aviso
        if ($id !== null && $seleccionada === null) {
            return redirect()
                ->route('solicitud.cliente.index')
                ->with('error', 'Esa solicitud no existe o ya no está disponible.');
        }

        // Al abrirla, sus notificaciones quedan leidas
        if ($seleccionada !== null) {
            $request->user()->notificaciones()
                ->where('solicitud_id', $seleccionada->id)
                ->update(['leida' => true]);
        }

        // Ids de las solicitudes que todavia tienen novedades sin leer (se marcan en la lista)
        $conNovedades = $request->user()->notificaciones()
            ->where('leida', false)
            ->pluck('solicitud_id');

        // Filtro de la lista (?estado=pendiente). Si viene cualquier otra cosa, se muestran todas
        $estado = $request->query('estado');
        if (! in_array($estado, ['pendiente', 'aceptada', 'presupuestada', 'en_chat'], true)) {
            $estado = null;
        }
        $lista = $estado === null ? $todas : $todas->where('estado', $estado);

        return view('solicitud.index-cliente', [
            'titulo' => 'Mis solicitudes',
            'lista' => $lista,
            'seleccionada' => $seleccionada,
            'estado' => $estado,
            'conteo' => $todas->countBy('estado'),
            'total' => $todas->count(),
            'conNovedades' => $conNovedades,
        ]);
    }

    /** El profesional acepta la solicitud */
    public function aceptar(Request $request, int $id): RedirectResponse
    {
        $solicitud = $this->pendienteDelProfesional($request, $id);

        $solicitud->update(['estado' => 'aceptada']);

        // Le avisamos al cliente
        Notificacion::create([
            'user_id' => $solicitud->cliente_id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'solicitud_aceptada',
            'mensaje' => $request->user()->usuario.' aceptó tu solicitud «'.$solicitud->titulo.'»',
        ]);

        return redirect()
            ->route('solicitud.show', $solicitud->id)
            ->with('exito', 'Aceptaste la solicitud. Le avisamos a '.$solicitud->cliente->usuario.'.');
    }

    /** El profesional rechaza la solicitud (queda guardada pero deja de verse) */
    public function rechazar(Request $request, int $id): RedirectResponse
    {
        $solicitud = $this->pendienteDelProfesional($request, $id);

        $solicitud->update(['estado' => 'rechazada']);

        // Le avisamos al cliente
        Notificacion::create([
            'user_id' => $solicitud->cliente_id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'solicitud_rechazada',
            'mensaje' => $request->user()->usuario.' rechazó tu solicitud «'.$solicitud->titulo.'»',
        ]);

        return redirect()
            ->route('solicitud.index')
            ->with('exito', 'Rechazaste la solicitud. Le avisamos a '.$solicitud->cliente->usuario.'.');
    }

    /** Busca una solicitud pendiente que sea de este profesional (si no, da error 404) */
    private function pendienteDelProfesional(Request $request, int $id): Solicitud
    {
        return $request->user()->profesional->solicitudes()
            ->where('estado', 'pendiente')
            ->findOrFail($id);
    }
}