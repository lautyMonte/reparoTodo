<?php

namespace App\Http\Controllers;

use App\Models\User;
use Chatify\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /** Lista de conversaciones del usuario (las personas con las que tiene una solicitud en chat) */
    public function index(Request $request): JsonResponse
    {
        $usuario = $request->user();

        $conversaciones = $usuario->contactosDeChat()->map(function ($otro) use ($usuario) {
            $ultimo = $usuario->conversationWith($otro)->messages()->latest()->first();

            return [
                'usuario_id' => $otro->id,
                'nombre' => $otro->nombreCompleto(),
                'avatar' => $otro->avatar(),
                'ultimo_mensaje' => $ultimo?->body,
                'fecha' => $ultimo?->created_at,
            ];
        })->sortByDesc('fecha')->values();

        return response()->json(['conversaciones' => $conversaciones]);
    }

    /** Muestra la conversacion con una persona: sus mensajes, del mas viejo al mas nuevo */
    public function show(Request $request, User $destinatario): JsonResponse
    {
        $usuario = $request->user();

        abort_unless($usuario->puedeChatearCon($destinatario), 403, 'No podés chatear con esta persona.');

        $mensajes = $usuario->conversationWith($destinatario)
            ->messages()
            ->oldest()
            ->get()
            ->map(fn ($mensaje) => $this->datosDelMensaje($mensaje, $usuario));

        return response()->json(['mensajes' => $mensajes]);
    }

    /** Guarda un mensaje nuevo en la conversacion con una persona */
    public function store(Request $request, User $destinatario): JsonResponse
    {
        $datos = $request->validate([
            'mensaje' => ['required', 'string', 'max:2000'],
        ]);

        $usuario = $request->user();

        abort_unless($usuario->puedeChatearCon($destinatario), 403, 'No podés chatear con esta persona.');

        $mensaje = $usuario->sendMessageTo($destinatario, $datos['mensaje']);

        return response()->json($this->datosDelMensaje($mensaje, $usuario));
    }

    /** Arma los datos de un mensaje para el JSON: texto, si es del usuario y cuando se envio */
    private function datosDelMensaje(Message $mensaje, User $usuario): array
    {
        return [
            'id' => $mensaje->id,
            'texto' => $mensaje->body,
            'mio' => (int) $mensaje->user_id === $usuario->id,
            'creado' => $mensaje->created_at,
        ];
    }
}