<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SolicitudController;
use Illuminate\Support\Facades\Route;

// Pagina de inicio (home): muestra los profesionales destacados (los de mejores resenas)
Route::get('/', [ProfesionalController::class, 'destacados'])->name('inicio');

// Inicio de sesion (el login paso de "/" a "/ingresar")
Route::get('/ingresar',[UsuarioController::class,'registro'])->name('usuario.registro')->middleware('guest');
Route::post('/ingresar',[UsuarioController::class,'verifica'])->name('usuario.verifica')->middleware('guest');
Route::post('/salir', [UsuarioController::class, 'salir'])
    ->middleware('auth')
    ->name('usuario.salir');
Route::get('/nuevo',[UsuarioController::class,'create'])->name('usuario.create')->middleware('guest');
Route::post('/nuevo',[UsuarioController::class,'store'])->name('usuario.store')->middleware('guest');

// Buscador de profesionales: es publico, lo puede ver cualquiera (con o sin sesion)
Route::get('/buscador', [ProfesionalController::class, 'index'])->name('profesional.index');

// Detalle de un profesional: tambien es publico (lo ve cualquiera, con o sin sesion)
Route::get('/profesional/{id}', [ProfesionalController::class, 'show'])->whereNumber('id')->name('profesional.show');

// Solicitud de trabajo: requiere un usuario autenticado
Route::middleware('auth')->group(function () {
    Route::get('/profesional/{id}/solicitar', [SolicitudController::class, 'create'])->whereNumber('id')->name('solicitud.create');
    Route::post('/profesional/{id}/solicitar', [SolicitudController::class, 'store'])->whereNumber('id')->name('solicitud.store');
});

// Para los chats
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {

    // Iniciar conversación
    Route::post('/iniciar/{destinatario}', [ChatController::class, 'iniciar'])
        ->name('iniciar');

    // Enviar mensaje
    Route::post('/enviar/{destinatario}', [ChatController::class, 'enviar'])
        ->name('enviar');

    // Recuperar historial
    Route::get('/mensajes/{destinatario}', [ChatController::class, 'mensajes'])
        ->name('mensajes');

});

