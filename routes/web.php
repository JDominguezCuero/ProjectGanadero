<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home.index');


Route::get('/prueba', function () {
    return ('¡Prueba Exitosa! Actividad de Git completada - Proyecto ProjectGanadero');
});

Route::get('/productos/{id}', [ProductoGanaderoController::class, 'show'])->name('productos.show');


Route::get('/perfil', [UsuariosController::class, 'index'])->name('perfil.index');
Route::post('/perfil/actualizar', [UsuariosController::class, 'actualizar'])->name('perfil.actualizar');