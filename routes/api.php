<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductosListaController;
use App\Http\Controllers\InventarioAlimentosController;

// routes/api.php
Route::resource('inventario', InventarioAlimentosController::class);