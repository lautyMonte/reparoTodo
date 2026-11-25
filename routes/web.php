<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\OficioController;
use App\Http\Controllers\ResenaController;
use Illuminate\Support\Facades\Route;


// Pagina de inicio (home): muestra los profesionales destacados (los de mejores resenas)
Route::get('/', [ProfesionalController::class, 'destacados'])->name('inicio');

// Inicio de sesion (el login paso de "/" a "/ingresar")
Route::get('/ingresar',[UsuarioController::class,'registro'])->name('usuario.registro');
Route::post('/ingresar',[UsuarioController::class,'verifica'])->name('usuario.verifica');
Route::get('/salir',[UsuarioController::class,'salir'])->name('usuario.salir');
Route::get('/nuevo',[UsuarioController::class,'create'])->name('usuario.create');
Route::post('/nuevo',[UsuarioController::class,'store'])->name('usuario.store');
Route::get('/oficios', [OficioController::class, 'index'])->name('oficio.index');

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

    Route::get('/cuenta/editar', [UsuarioController::class, 'edit'])->name('usuario.edit');
    Route::put('/cuenta/actualizar', [UsuarioController::class, 'update'])->name('usuario.update');
});

Route::middleware('profesional')->group(function () {
    Route::get('/bandeja', [SolicitudController::class, 'index'])->name('solicitud.index');
    Route::get('/bandeja/{id}', [SolicitudController::class, 'show'])->whereNumber('id')->name('solicitud.show');
    Route::post('/bandeja/{id}/aceptar', [SolicitudController::class, 'aceptar'])->whereNumber('id')->name('solicitud.aceptar');
    Route::post('/bandeja/{id}/rechazar', [SolicitudController::class, 'rechazar'])->whereNumber('id')->name('solicitud.rechazar');
    Route::get('/bandeja/{id}/presupuesto', [PresupuestoController::class, 'create'])->whereNumber('id')->name('presupuesto.create');
    Route::post('/bandeja/{id}/presupuesto', [PresupuestoController::class, 'store'])->whereNumber('id')->name('presupuesto.store');

    Route::get('/mi-perfil/editar', [ProfesionalController::class, 'edit'])->name('profesional.edit');
    Route::put('/mi-perfil/actualizar', [ProfesionalController::class, 'update'])->name('profesional.update');
});


Route::delete('/resenas/{resena}', [ResenaController::class, 'destroy'])
    ->middleware('auth')
    ->name('resenas.destroy');

// Cierre del trabajo: lo proponen y lo responden tanto el cliente como el profesional
Route::middleware('auth')->group(function () {
    Route::post('/solicitudes/{id}/cierre', [SolicitudController::class, 'proponerCierre'])->whereNumber('id')->name('cierre.proponer');
    Route::post('/solicitudes/{id}/cierre/aceptar', [SolicitudController::class, 'aceptarCierre'])->whereNumber('id')->name('cierre.aceptar');
    Route::post('/solicitudes/{id}/cierre/rechazar', [SolicitudController::class, 'rechazarCierre'])->whereNumber('id')->name('cierre.rechazar');
    Route::delete('/solicitudes/{id}/cierre', [SolicitudController::class, 'retirarCierre'])->whereNumber('id')->name('cierre.retirar');
});

// Notificaciones: para cualquier usuario con sesion iniciada
Route::middleware('auth')->group(function () {
    Route::post('/notificaciones/leer', [NotificacionController::class, 'leerTodas'])->name('notificacion.leer');
    Route::get('/notificaciones/resumen', [NotificacionController::class, 'resumen'])->name('notificacion.resumen');
});

// Para los chats: solo entre personas con una solicitud en chat
Route::middleware('auth')->prefix('chat')->name('chat.')->group(function () {

    // Lista de conversaciones del usuario
    Route::get('/conversaciones', [ChatController::class, 'index'])->name('index');

    // Mensajes de la conversacion con una persona
    Route::get('/conversaciones/{destinatario}', [ChatController::class, 'show'])->name('show');

    // Enviar un mensaje a esa persona
    Route::post('/conversaciones/{destinatario}', [ChatController::class, 'store'])->name('store');

});

// Preguntas frecuentes
Route::view('/faq', 'faq')->name('faq');

// Terminos y condiciones
Route::view('/tyc', 'tyc')->name('tyc');
