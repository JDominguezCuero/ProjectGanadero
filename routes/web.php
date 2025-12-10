<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuariosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductosListaController;
use App\Http\Controllers\InventarioAlimentosController;
use App\Http\Controllers\NotificacionesController;
use App\Http\Controllers\GestionUsuariosController;


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
| RUTAS DE ADMINISTRAR USUARIOS
|--------------------------------------------------------------------------
*/

// Login (mostrar formulario)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

// Procesar login
Route::post('/login', [GestionUsuariosController::class, 'login'])->name('login.procesar');

// Logout
Route::get('/logout', [GestionUsuariosController::class, 'logout'])->name('logout');


/*
|--------------------------------------------------------------------------
| RUTA DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('auth')->name('dashboard');


/*
|--------------------------------------------------------------------------
| RUTAS DE REGISTRO DE USUARIOS (PÚBLICO)
|--------------------------------------------------------------------------
*/

Route::post('/registrarse', [GestionUsuariosController::class, 'store'])
    ->name('usuarios.store');


/*
|--------------------------------------------------------------------------
| RUTAS CRUD DE USUARIOS (ADMIN)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Listar usuarios
    Route::get('/usuarios', [GestionUsuariosController::class, 'index'])
        ->name('usuarios.index');

    // Agregar usuario
    Route::post('/usuarios/agregar', [GestionUsuariosController::class, 'agregar'])
        ->name('usuarios.agregar');

    // Editar usuario
    Route::post('/usuarios/editar', [GestionUsuariosController::class, 'update'])->name('usuarios.editar');


    // Eliminar usuario
    Route::get('/usuarios/{user}', [GestionUsuariosController::class, 'destroy'])
        ->name('usuarios.eliminar');
});


/*
|--------------------------------------------------------------------------
| RUTAS RECUPERAR CONTRASEÑA
|--------------------------------------------------------------------------
*/

// Enviar enlace al correo
Route::post('/recuperar', [GestionUsuariosController::class, 'enviarEnlaceRestablecimiento'])
    ->name('password.enviar');

// Mostrar formulario de nueva contraseña
Route::get('/restablecer', [GestionUsuariosController::class, 'mostrarFormularioNuevaContrasena'])
    ->name('password.reset.form');

// Restablecer contraseña
Route::post('/restablecer', [GestionUsuariosController::class, 'restablecer'])
    ->name('password.reset.enviar');