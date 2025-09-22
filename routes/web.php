<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba', function () {
    return ('¡Prueba Exitosa! Actividad de Git completada - Proyecto ProjectGanadero');
});

Route::get('/perfil', [UsuariosController::class, 'index'])->name('perfil.index');
Route::post('/perfil/actualizar', [UsuariosController::class, 'actualizar'])->name('perfil.actualizar');