<?php

namespace App\Http\Controllers;

use App\Models\Oficio;
use Illuminate\View\View;

class OficioController extends Controller
{
    public function index(): View
    {
        $oficios = Oficio::orderBy('tipo')->get();

        return view('oficio.index', [
            'titulo' => 'Seleccionar oficio',
            'oficios' => $oficios,
        ]);
    }
}
