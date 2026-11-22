<?php

namespace App\Http\Controllers;

use App\Models\Localidad;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocalidadController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $datos = $request->validate([
            'jurisdiccion_id' => [
                'required',
                'integer',
                'exists:jurisdicciones,id',
            ],
        ]);

        $localidades = Localidad::where(
            'jurisdiccion_id',
            $datos['jurisdiccion_id']
        )
            ->orderBy('nombre')
            ->get(['id', 'nombre']);

        return response()->json($localidades);
    }
}
