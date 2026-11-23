<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\Presupuesto;
use App\Models\Solicitud;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PresupuestoController extends Controller
{
    /** Muestra el formulario para armar el presupuesto de una solicitud aceptada */
    public function create(Request $request, int $id): View
    {
        $solicitud = $this->aceptadaDelProfesional($request, $id);

        return view('presupuesto.create', [
            'solicitud' => $solicitud,
            'titulo' => 'Armar presupuesto',
        ]);
    }

    /** Valida y guarda el presupuesto, y le avisa al cliente */
    public function store(Request $request, int $id): RedirectResponse
    {
        $solicitud = $this->aceptadaDelProfesional($request, $id);

        $datos = $request->validate([
            'monto' => ['required', 'integer', 'min:1', 'max:99999999'],
            'plazo_dias' => ['required', 'integer', 'min:1', 'max:365'],
            'detalle' => ['required', 'string', 'max:1000'],
        ], [
            'monto.required' => 'El monto es obligatorio',
            'monto.integer' => 'El monto tiene que ser un número entero',
            'monto.min' => 'El monto tiene que ser mayor a 0',
            'monto.max' => 'El monto es demasiado alto',
            'plazo_dias.required' => 'El plazo es obligatorio',
            'plazo_dias.integer' => 'El plazo tiene que ser un número entero de días',
            'plazo_dias.min' => 'El plazo tiene que ser de al menos 1 día',
            'plazo_dias.max' => 'El plazo puede ser de hasta 365 días',
            'detalle.required' => 'El detalle es obligatorio',
            'detalle.max' => 'El detalle puede tener hasta 1000 caracteres',
        ]);

        Presupuesto::create([
            'solicitud_id' => $solicitud->id,
            'monto' => $datos['monto'],
            'plazo_dias' => $datos['plazo_dias'],
            'detalle' => $datos['detalle'],
        ]);

        $solicitud->update(['estado' => 'presupuestada']);

        // Le avisamos al cliente
        Notificacion::create([
            'user_id' => $solicitud->cliente_id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'presupuesto_nuevo',
            'mensaje' => $request->user()->usuario.' te envió un presupuesto para «'.$solicitud->titulo.'»',
        ]);

        return redirect()
            ->route('solicitud.show', $solicitud->id)
            ->with('exito', 'Enviaste el presupuesto. Le avisamos a '.$solicitud->cliente->usuario.'.');
    }

    /** Busca una solicitud aceptada que sea de este profesional (si no, da error 404) */
    private function aceptadaDelProfesional(Request $request, int $id): Solicitud
    {
        return $request->user()->profesional->solicitudes()
            ->where('estado', 'aceptada')
            ->with('cliente')
            ->findOrFail($id);
    }

    /** El cliente acepta el presupuesto: la solicitud pasa a chat */
    public function aceptar(Request $request, int $id): RedirectResponse
    {
        $solicitud = $this->presupuestadaDelCliente($request, $id);

        $solicitud->update(['estado' => 'en_chat']);

        // Le avisamos al profesional
        Notificacion::create([
            'user_id' => $solicitud->profesional->user_id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'presupuesto_aceptado',
            'mensaje' => $request->user()->usuario.' aceptó tu presupuesto para «'.$solicitud->titulo.'»',
        ]);

        return redirect()
            ->route('solicitud.cliente.show', $solicitud->id)
            ->with('exito', 'Aceptaste el presupuesto. Le avisamos a '.$solicitud->profesional->user->usuario.'.');
    }

    /** El cliente rechaza el presupuesto (queda guardado pero deja de verse) */
    public function rechazar(Request $request, int $id): RedirectResponse
    {
        $solicitud = $this->presupuestadaDelCliente($request, $id);

        $solicitud->update(['estado' => 'rechazada']);

        // Le avisamos al profesional
        Notificacion::create([
            'user_id' => $solicitud->profesional->user_id,
            'solicitud_id' => $solicitud->id,
            'tipo' => 'presupuesto_rechazado',
            'mensaje' => $request->user()->usuario.' rechazó tu presupuesto para «'.$solicitud->titulo.'»',
        ]);

        return redirect()
            ->route('solicitud.cliente.index')
            ->with('exito', 'Rechazaste el presupuesto. Le avisamos a '.$solicitud->profesional->user->usuario.'.');
    }

    /** Busca una solicitud presupuestada que sea de este cliente (si no, da error 404) */
    private function presupuestadaDelCliente(Request $request, int $id): Solicitud
    {
        return $request->user()->solicitudes()
            ->where('estado', 'presupuestada')
            ->with('profesional.user')
            ->findOrFail($id);
    }
}