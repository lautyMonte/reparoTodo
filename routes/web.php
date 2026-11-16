<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//inicio de la pagina
Route::get('/',[UsuarioController::class,'index'])->name('usuario.index');
Route::post('/',[UsuarioController::class,'verifica'])->name('usuario.verifica');

//muestra todos los profesionales cargados en la pagina
Route::get('/buscador',[ProfesionalController::class,'index'])->name('profesional.listado');
Route::get('/profesional/{id}',[ProfesionalController::class,'perfil'])->whereNumber('id')->name('profesional.perfil');

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
