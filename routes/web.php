<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/prueba', function () {
    return ('¡Prueba Exitosa! Actividad de Git completada - Proyecto ProjectGanadero');
});
