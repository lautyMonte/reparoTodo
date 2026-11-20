<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Pagina de inicio (home): no necesita datos de ningun modelo, solo muestra la vista
Route::get('/', function () {
    return view('inicio', ['titulo' => 'Inicio']);
})->name('inicio');

// Inicio de sesion (el login paso de "/" a "/ingresar")
Route::get('/ingresar',[UsuarioController::class,'registro'])->name('usuario.registro');
Route::post('/ingresar',[UsuarioController::class,'verifica'])->name('usuario.verifica');
Route::get('/salir',[UsuarioController::class,'salir'])->name('usuario.salir');
Route::get('/nuevo',[UsuarioController::class,'create'])->name('usuario.create');
Route::post('/nuevo',[UsuarioController::class,'store'])->name('usuario.store');


// Ingreso a la pagina web de forma segura (autenticacion)
Route::middleware(['auth', 'cliente'])->group(function () {
   Route::get('/buscador',[ProfesionalController::class,'index'])->name('profesional.listado');
     
});
Route::middleware(['auth', 'profesional'])->group(function () {
   Route::get('/profesional/{id}',[ProfesionalController::class,'perfil'])->whereNumber('id')->name('profesional.perfil');
     
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

