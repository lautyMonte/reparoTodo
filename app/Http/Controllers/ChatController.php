<?php

namespace App\Http\Controllers;
use App\Models\User;

use Chatify\Models\Conversation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
     /**
     * Iniciar o recuperar una conversación.
     */
    public function iniciar(Request $request, User $destinatario)
    {
        $emisor = $request->user();

        abort_if($emisor->id === $destinatario->id, 403);

        $this->validarParticipantes($emisor, $destinatario);

        $conversacion = $emisor->iniciarConversacion($destinatario);

        return response()->json([
            'conversacion' => $conversacion,
        ]);
    }

    /**
     * Enviar un mensaje.
     */
    public function enviar(Request $request, User $destinatario)
    {
        $datos = $request->validate([
            'mensaje' => 'required|string|max:2000',
        ]);

        $emisor = $request->user();

        abort_if($emisor->id === $destinatario->id, 403);

        $this->validarParticipantes($emisor, $destinatario);

        // Comprobar que la conversación ya exista
        $conversacion = $this->buscarConversacion(
            $emisor,
            $destinatario
        );

        abort_unless($conversacion, 404,
            'No existe una conversación iniciada.'
        );

        $resultado = $emisor->enviarMensaje(
            $destinatario,
            $datos['mensaje']
        );

        return response()->json([
            'mensaje' => 'Mensaje enviado correctamente',
            'resultado' => $resultado,
        ]);
    }

    /**
     * Recuperar el historial de mensajes.
     */
    public function mensajes(Request $request,User $destinatario)
    {
        $emisor = $request->user();

        abort_if($emisor->id === $destinatario->id, 403);

        $this->validarParticipantes($emisor, $destinatario);

        // Buscar la conversación existente
        $conversacion = $this->buscarConversacion(
            $emisor,
            $destinatario
        );

        abort_unless($conversacion, 404,
            'No existe una conversación entre estos usuarios.'
        );

        // Recuperar todos los mensajes ordenados cronológicamente
        $mensajes = $conversacion->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'conversacion' => $conversacion->id,
            'mensajes' => $mensajes,
        ]);
    }

    /**
     * Buscar una conversación directa existente.
     */
    private function buscarConversacion(
        User $usuario,
        User $destinatario
    ): ?Conversation {

        return Conversation::query()
            ->direct()
            ->whereHas('participants', function ($query) use ($usuario) {
                $query->where('user_id', $usuario->id);
            })
            ->whereHas('participants', function ($query) use ($destinatario) {
                $query->where('user_id', $destinatario->id);
            })
            ->with('messages')
            ->first();
    }

    /**
     * Validar los tipos de usuarios.
     */
    private function validarParticipantes(
        User $emisor,
        User $destinatario
    ): void {

        $tiposValidos =
            ($emisor->tipo === 'cliente' &&
             $destinatario->tipo === 'profesional')
            ||
            ($emisor->tipo === 'profesional' &&
             $destinatario->tipo === 'cliente');

        abort_unless($tiposValidos, 403);
    }
}
