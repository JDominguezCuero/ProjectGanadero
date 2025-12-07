<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductosListaController;
use App\Http\Controllers\InventarioAlimentosController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\PerfilController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home.index');

Route::get('/prueba', function () {
    return '¡Prueba Exitosa! Actividad de Git completada - Proyecto ProjectGanadero';
});

Route::get('/campanas', function () { 
    return view('campanas'); 
})->name('campanas');

Route::get('/nosotros', function () { 
    return view('nosotros'); 
})->name('nosotros');

Route::get('/contacto', function () { 
    return view('contacto'); 
})->name('contacto');

Route::get('/autenticacion', function () { 
    return view('usuarios.autenticacion'); 
})->name('autenticacion');

Route::get('/perfil', [UsuariosController::class, 'index'])->name('perfil.index');
Route::post('/perfil/actualizar', [UsuariosController::class, 'actualizar'])->name('perfil.actualizar');

Route::match(['get','post'],'/auth/login', [UsuariosController::class, 'login'])->name('auth.login');
Route::match(['get','post'],'/auth/registro', [UsuariosController::class, 'registro'])->name('auth.registro');
Route::match(['get','post'],'/auth/logout', [UsuariosController::class, 'logout'])->name('auth.logout');

/*
|--------------------------------------------------------------------------
| Productos - público
|--------------------------------------------------------------------------
*/
// Listado público / filtros
Route::get('/productos', [ProductosListaController::class, 'index'])->name('productos.index');

// Detalle JSON público (ruta consistente): /productos/{id}/detalle
Route::get('/productos/{id}/detalle', [ProductosListaController::class, 'detalle'])->name('productos.detalle');

/*
|--------------------------------------------------------------------------
| Productos - gestión (protegido)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Vista de gestión (admin/mi-usuario)
    Route::get('/productos/manage', [ProductosListaController::class, 'manage'])->name('productos.manage');

    // Crear producto
    Route::post('/productos', [ProductosListaController::class, 'store'])->name('productos.store');

    // Actualizar producto
    Route::put('/productos/{id}', [ProductosListaController::class, 'update'])->name('productos.update');

    // Eliminar producto
    Route::delete('/productos/{id}', [ProductosListaController::class, 'destroy'])->name('productos.destroy');
});

/*
|--------------------------------------------------------------------------
| Inventario_Alimento - gestión (protegido)
|--------------------------------------------------------------------------
*/
Route::resource('inventario', InventarioAlimentosController::class);

/*
|--------------------------------------------------------------------------
| Notificaciones
|--------------------------------------------------------------------------
*/

Route::get('/notificaciones', [NotificacionesController::class, 'index'])
    ->name('notificaciones.index');


/*
|--------------------------------------------------------------------------
| Editar Perfil de Usuario
|--------------------------------------------------------------------------
*/
Route::get('/perfil/{id?}', [PerfilController::class, 'listarUsuario'])
    ->name('perfil.listarUsuario');

Route::post('/perfil/actualizar', [PerfilController::class, 'actualizar'])
    ->name('perfil.actualizar');
 