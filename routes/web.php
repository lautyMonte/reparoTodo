<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Pagina de inicio (home): muestra los profesionales destacados (los de mejores resenas)
Route::get('/', [ProfesionalController::class, 'destacados'])->name('inicio');

// Inicio de sesion (el login paso de "/" a "/ingresar")
Route::get('/ingresar',[UsuarioController::class,'registro'])->name('usuario.registro');
Route::post('/ingresar',[UsuarioController::class,'verifica'])->name('usuario.verifica');
Route::get('/salir',[UsuarioController::class,'salir'])->name('usuario.salir');
Route::get('/nuevo',[UsuarioController::class,'create'])->name('usuario.create');
Route::post('/nuevo',[UsuarioController::class,'store'])->name('usuario.store');

// Buscador de profesionales: es publico, lo puede ver cualquiera (con o sin sesion)
Route::get('/buscador', [ProfesionalController::class, 'index'])->name('profesional.index');
Route::get('/buscador/profesional',[ProfesionalController::class,'buscar'])->name('profesional.busca');

// Detalle de un profesional: tambien es publico (lo ve cualquiera, con o sin sesion)
Route::get('/profesional/{id}', [ProfesionalController::class, 'show'])->whereNumber('id')->name('profesional.show');

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

