<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PresupuestoController;
use Illuminate\Support\Facades\Route;

// Pagina de inicio (home): muestra los profesionales destacados (los de mejores resenas)
Route::get('/', [ProfesionalController::class, 'destacados'])->name('inicio');

// Inicio de sesion (el login paso de "/" a "/ingresar")
Route::get('/ingresar',[UsuarioController::class,'registro'])->name('usuario.registro');
Route::post('/ingresar',[UsuarioController::class,'verifica'])->name('usuario.verifica');
Route::get('/salir',[UsuarioController::class,'salir'])->name('usuario.salir');
Route::get('/nuevo',[UsuarioController::class,'create'])->name('usuario.create');
Route::post('/nuevo',[UsuarioController::class,'store'])->name('usuario.store');

// Mostrar formulario de contacto
Route::view('/contacto', 'contacto')->name('contacto');

// Procesar formulario de contacto
Route::post('/contacto', [ContactoController::class, 'store'])->name('contacto.store');

// Buscador de profesionales: es publico, lo puede ver cualquiera (con o sin sesion)
Route::get('/buscador', [ProfesionalController::class, 'index'])->name('profesional.index');

//filtrar por nombre, oficio, localidad y puntuacion
Route::get('/profesionales/filtrar', [ProfesionalController::class, 'filtrar'])->name('profesional.filtrar');

// Detalle de un profesional: tambien es publico (lo ve cualquiera, con o sin sesion)
Route::get('/profesional/{id}', [ProfesionalController::class, 'show'])->whereNumber('id')->name('profesional.show');

// Solicitud de trabajo: solo la puede hacer un cliente con sesion iniciada
Route::middleware('cliente')->group(function () {
    Route::get('/profesional/{id}/solicitar', [SolicitudController::class, 'create'])->whereNumber('id')->name('solicitud.create');
    Route::post('/profesional/{id}/solicitar', [SolicitudController::class, 'store'])->whereNumber('id')->name('solicitud.store');
    Route::get('/mis-solicitudes', [SolicitudController::class, 'indexCliente'])->name('solicitud.cliente.index');
    Route::get('/mis-solicitudes/{id}', [SolicitudController::class, 'showCliente'])->whereNumber('id')->name('solicitud.cliente.show');
    Route::post('/mis-solicitudes/{id}/presupuesto/aceptar', [PresupuestoController::class, 'aceptar'])->whereNumber('id')->name('presupuesto.aceptar');
    Route::post('/mis-solicitudes/{id}/presupuesto/rechazar', [PresupuestoController::class, 'rechazar'])->whereNumber('id')->name('presupuesto.rechazar');
});

Route::middleware('profesional')->group(function () {
    Route::get('/bandeja', [SolicitudController::class, 'index'])->name('solicitud.index');
    Route::get('/bandeja/{id}', [SolicitudController::class, 'show'])->whereNumber('id')->name('solicitud.show');
    Route::post('/bandeja/{id}/aceptar', [SolicitudController::class, 'aceptar'])->whereNumber('id')->name('solicitud.aceptar');
    Route::post('/bandeja/{id}/rechazar', [SolicitudController::class, 'rechazar'])->whereNumber('id')->name('solicitud.rechazar');
    Route::get('/bandeja/{id}/presupuesto', [PresupuestoController::class, 'create'])->whereNumber('id')->name('presupuesto.create');
    Route::post('/bandeja/{id}/presupuesto', [PresupuestoController::class, 'store'])->whereNumber('id')->name('presupuesto.store');
});

// Notificaciones: para cualquier usuario con sesion iniciada
Route::middleware('auth')->group(function () {
    Route::post('/notificaciones/leer', [NotificacionController::class, 'leerTodas'])->name('notificacion.leer');
    Route::get('/notificaciones/resumen', [NotificacionController::class, 'resumen'])->name('notificacion.resumen');
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

// Preguntas frecuentes
Route::view('/faq', 'faq')->name('faq');

// Terminos y condiciones
Route::view('/tyc', 'tyc')->name('tyc');
