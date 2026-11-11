<?php

use App\Http\Controllers\ProfesionalController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

//inicio de la pagina
Route::get('/',[UsuarioController::class,'index'])->name('usuario.index');
Route::post('/',[UsuarioController::class,'verifica'])->name('usuario.verifica');

//muestra todos los profesionales cargados en la pagina
Route::get('/buscador',[ProfesionalController::class,'index'])->name('profesional.index');


// // Búsqueda en todo el catálogo (barra superior).
// Route::get('/skins', [SkinController::class, 'index'])->name('skins.index');

// // Ruta que responde JSON (no una vista) para el pedido AJAX (fetch) del buscador.
// Route::get('/api/skins/sugerencias', [SkinController::class, 'sugerencias'])->name('skins.sugerencias');