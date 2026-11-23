<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /** Marca como leidas todas las notificaciones del usuario */
    public function leerTodas(Request $request): RedirectResponse
    {
        $request->user()->notificaciones()->where('leida', false)->update(['leida' => true]);

        return back();
    }

    /** Devuelve en JSON los numeros de la campana y de la bandeja (lo pide el JavaScript con fetch) */
    public function resumen(Request $request): JsonResponse
    {
        $usuario = $request->user();

        return response()->json([
            'sin_leer' => $usuario->notificacionesSinLeer(),
            'pendientes' => $usuario->pendientesDeRespuesta(),
        ]);
    }
}