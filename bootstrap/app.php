<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

//Aca colocamos los middlewares que creamos
use App\Http\Middleware\SoloProfesional;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //Aca colocamos los middlewares que creamos
        $middleware->alias([
            'profesional'=>SoloProfesional::class
        ]);
        //Si alguien sin sesion entra a una ruta protegida, lo mandamos al login
        $middleware->redirectGuestsTo(fn () => route('usuario.registro'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
