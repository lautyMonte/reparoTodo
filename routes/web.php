
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\LocalidadController;

/*
|--------------------------------------------------------------------------
| Página de inicio
|--------------------------------------------------------------------------
*/

Route::get('/', [ProfesionalController::class, 'destacados'])
    ->name('inicio');

/*
|--------------------------------------------------------------------------
| Registro y autenticación de usuarios
|--------------------------------------------------------------------------
*/

// Iniciar sesión
Route::get('/ingresar', [UsuarioController::class, 'registro'])
    ->middleware('guest')
    ->name('usuario.registro');

Route::post('/ingresar', [UsuarioController::class, 'verifica'])
    ->middleware('guest')
    ->name('usuario.verifica');

// Crear cuenta
Route::get('/nuevo', [UsuarioController::class, 'create'])
    ->middleware('guest')
    ->name('usuario.create');

Route::post('/nuevo', [UsuarioController::class, 'store'])
    ->middleware('guest')
    ->name('usuario.store');

// Cerrar sesión
Route::post('/salir', [UsuarioController::class, 'salir'])
    ->middleware('auth')
    ->name('usuario.salir');

/*
|--------------------------------------------------------------------------
| Profesionales: páginas públicas
|--------------------------------------------------------------------------
*/

// Buscador de profesionales publicados
Route::get('/buscador', [ProfesionalController::class, 'index'])
    ->name('profesional.index');

// Detalle público de un profesional publicado
Route::get('/profesional/{id}', [ProfesionalController::class, 'show'])
    ->whereNumber('id')
    ->name('profesional.show');

/*
|--------------------------------------------------------------------------
| Perfil profesional propio (requiere autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Ver mi perfil profesional
    Route::get('/mi-perfil-profesional', [
        ProfesionalController::class,
        'miPerfil'
    ])->name('profesional.mi-perfil');

    // Crear mi perfil como borrador
    Route::post('/mi-perfil-profesional', [
        ProfesionalController::class,
        'crearBorrador'
    ])->name('profesional.crear-borrador');

    // Actualizar los datos de mi perfil profesional
    Route::patch('/mi-perfil-profesional', [
        ProfesionalController::class,
        'actualizarPerfil'
    ])->name('profesional.actualizar');

    // Obtener localidades por provincia mediante AJAX
    Route::get('/geografia/localidades', [
        LocalidadController::class,
        'index'
    ])->name('geografia.localidades');

});

/*
|--------------------------------------------------------------------------
| Solicitudes de trabajo (requieren autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profesional/{id}/solicitar', [
        SolicitudController::class,
        'create'
    ])
        ->whereNumber('id')
        ->name('solicitud.create');

    Route::post('/profesional/{id}/solicitar', [
        SolicitudController::class,
        'store'
    ])
        ->whereNumber('id')
        ->name('solicitud.store');

});

/*
|--------------------------------------------------------------------------
| Chat (requiere autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('chat')
    ->name('chat.')
    ->group(function () {

        // Iniciar conversación
        Route::post('/iniciar/{destinatario}', [
            ChatController::class,
            'iniciar'
        ])->name('iniciar');

        // Enviar mensaje
        Route::post('/enviar/{destinatario}', [
            ChatController::class,
            'enviar'
        ])->name('enviar');

        // Recuperar historial
        Route::get('/mensajes/{destinatario}', [
            ChatController::class,
            'mensajes'
        ])->name('mensajes');

    });
