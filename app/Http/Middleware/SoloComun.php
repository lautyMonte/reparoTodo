<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SoloComun
{
    /**
     * 
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->tipo!=='comun') {
            return redirect('usuario.registro')->with('error','Acceso denegado');
            }
        return $next($request);
    }
}
