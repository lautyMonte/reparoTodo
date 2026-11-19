<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

//inicio de la pagina
Route::get('/',[UsuarioController::class,'registro'])->name('usuario.registro');
Route::post('/',[UsuarioController::class,'verifica'])->name('usuario.verifica');
Route::get('/salir',[UsuarioController::class,'salir'])->name('usuario.salir');
Route::get('/nuevo',[UsuarioController::class,'create'])->name('usuario.create');
Route::post('/nuevo',[UsuarioController::class,'store'])->name('usuario.store');


//ingreso a la pagina web de forma segura (autenticacion)
Route::middleware(['auth', 'comun'])->group(function () {
   Route::get('/buscador',[ProfesionalController::class,'index'])->name('profesional.listado');
     
});
Route::middleware(['auth', 'profesional'])->group(function () {
   Route::get('/profesional/{id}',[ProfesionalController::class,'perfil'])->whereNumber('id')->name('profesional.perfil');
     
});

//para los chats
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

