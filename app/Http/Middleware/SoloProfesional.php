<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloProfesional
{
    /**
     * 
     *
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return redirect()->route('usuario.registro')->with('error','Acceso denegado');
        }
        if ($request->user()->tipo !== 'profesional') {
            return redirect()->route('inicio')->with('error','Acceso denegado');
        }
        return $next($request);
    }
}
