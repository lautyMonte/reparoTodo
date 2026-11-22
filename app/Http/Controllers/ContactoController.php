<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ContactoController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Validar los datos
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'motivo' => [
                'required',
                Rule::in([
                    'problema',
                    'reportar',
                    'sugerencia',
                    'otro',
                ]),
            ],
            'asunto' => ['required_if:motivo,otro', 'nullable', 'string', 'max:150'],
            'mensaje' => ['required', 'string', 'max:5000'],
        ]);

        // Definir el asunto
        if ($datos['motivo'] === 'otro') {
            $asuntoCorreo = $datos['asunto'];
        } else {
            $asuntos = [
                'problema' => 'Problema con mi cuenta',
                'reportar' => 'Reportar a un profesional',
                'sugerencia' => 'Comentarios & Sugerencias',
            ];
            $asuntoCorreo = $asuntos[$datos['motivo']];
        }

        // Enviar el correo
        Mail::raw(
            "Nombre: {$datos['nombre']}\n".
            "Correo: {$datos['email']}\n".
            'Teléfono: '.($datos['telefono'] ?? 'No indicado')."\n".
            "Motivo: {$datos['motivo']}\n\n".
            "Mensaje:\n{$datos['mensaje']}",

            function ($mail) use ($datos, $asuntoCorreo) {
                $mail->to('contactoreparotodo@gmail.com')
                    ->subject($asuntoCorreo)
                    ->replyTo($datos['email'], $datos['nombre']);
            }
        );

        // Redirigir
        return redirect()->route('contacto')
            ->with('exito', 'Tu mensaje fue enviado correctamente.');
    }
}
