<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductosListaController;

Route::get('/', [HomeController::class, 'index'])->name('home.index');


Route::get('/prueba', function () {
    return ('¡Prueba Exitosa! Actividad de Git completada - Proyecto ProjectGanadero');
});

// Route::get('/productos', function () { 
//     return view('productos'); 
// })->name('productos');

Route::get('/campanas', function () { 
    return view('campanas'); 
})->name('campanas');

Route::get('/nosotros', function () { 
    return view('nosotros'); 
})->name('nosotros');

Route::get('/contacto', function () { 
    return view('contacto'); 
})->name('contacto');

// Route::get('/ListaProductos', function () { 
//     return view('productos_lista'); 
// })->name('productos.index');

Route::get('/autenticacion', function () { 
    return view('usuarios.autenticacion'); 
})->name('autenticacion');




Route::get('/perfil', [UsuariosController::class, 'index'])->name('perfil.index');
Route::post('/perfil/actualizar', [UsuariosController::class, 'actualizar'])->name('perfil.actualizar');


// Route::get('/notificaciones/listar', [NotificacionController::class, 'listar'])->name('notificaciones.listar');
// Route::post('/notificaciones/eliminar', [NotificacionController::class, 'eliminar'])->name('notificaciones.eliminar');
// Route::post('/notificaciones/insertar', [NotificacionController::class, 'insertar'])->name('notificaciones.insertar');


Route::get('/productos', [ProductosListaController::class, 'index'])->name('productos');
Route::get('/productos/detalle/{id}', [ProductosListaController::class, 'detalle'])->name('productos.detalle');
Route::match(['get','post'],'/auth/login', [UsuariosController::class, 'login'])->name('auth.login');
