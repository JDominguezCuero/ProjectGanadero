<?php

namespace App\Http\Controllers;

use App\Models\Usuarios;
use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

class GestionUsuariosController extends Controller
{
    /* ============================================================
     * LISTAR USUARIOS
     * ============================================================ */
    public function index()
{
    // Obtener todos los roles (igual que en tu ejemplo original)
    $roles = Roles::all();

    // Obtener todos los usuarios junto con su relación
    $usuarios = Usuarios::with('rol')->get();

    // Retornar la vista que ya estás usando
    return view('gestionUsuarios.gestionUsuarios', compact('usuarios', 'roles'));
}

    /* ============================================================
     * LOGIN
     * ============================================================ */
    public function login(Request $request)
    {
        $request->validate([
            'correoElectronicoLogin' => 'required|email',
            'contrasenaLogin' => 'required'
        ]);

        $credenciales = [
            'correo_usuario' => $request->correoElectronicoLogin,
            'password' => $request->contrasenaLogin
        ];

        if (Auth::attempt($credenciales)) {
            $user = Auth::user();

            if ($user->estado !== 'Activo') {
                Auth::logout();
                return back()->withErrors('El usuario no está activo.');
            }

            return redirect()->route('dashboard');
        }

        return back()->withErrors('Credenciales incorrectas.');
    }

    /* ============================================================
     * REGISTRO
     * ============================================================ */
    public function store(Request $request)
    {
        $request->validate([
            'nombreCompleto' => 'required',
            'correoElectronico' => 'required|email|unique:usuarios,correo_usuario',
            'usuario' => 'required',
            'contrasena' => 'required|min:8',
        ]);

        $user = new Usuarios();
        $user->nombreCompleto = $request->nombreCompleto;
        $user->correo_usuario = $request->correoElectronico;
        $user->nombre_usuario = $request->usuario;
        $user->contrasena_usuario = Hash::make($request->contrasena);
        $user->imagen_url_usuario = '/modules/auth/perfiles/profileDefault.png';
        $user->estado = 'Activo';
        $user->id_rol = 2; // Rol por defecto

        $user->save();

        return redirect()->back()->with('success', 'Registro exitoso');
    }

    /* ============================================================
     * AGREGAR USUARIO (ADMIN)
     * ============================================================ */
    public function agregar(Request $request)
    {
        $request->validate([
            'nombreCompleto' => 'required',
            'nombre_usuario' => 'required',
            'correo_usuario' => 'required|email|unique:usuarios,correo_usuario',
            'telefono_usuario' => 'required',
            'rol_id' => 'required',
            'direccion_usuario' => 'required',
            'estado' => 'required',
            'contrasena' => 'required|min:8',
            'imagen' => 'nullable|image|mimes:jpg,png,jpeg,webp'
        ]);

        $rutaImagen = '/modules/auth/perfiles/profileDefault.png';

        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('perfiles', 'public');
            $rutaImagen = '/storage/' . $rutaImagen;
        }

        Usuarios::create([
            'nombreCompleto' => $request->nombreCompleto,
            'nombre_usuario' => $request->nombre_usuario,
            'correo_usuario' => $request->correo_usuario,
            'telefono_usuario' => $request->telefono_usuario,
            'id_rol' => $request->rol_id,
            'direccion_usuario' => $request->direccion_usuario,
            'estado' => $request->estado,
            'contrasena_usuario' => Hash::make($request->contrasena),
            'imagen_url_usuario' => $rutaImagen
        ]);

        return redirect()->route('usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    /* ============================================================
     * EDITAR USUARIO
     * ============================================================ */
    public function update(Request $request, Usuarios $user)
    {
        $request->validate([
            'nombreCompleto' => 'required',
            'nombre_usuario' => 'required',
            'correo_usuario' => 'required|email|unique:usuarios,correo_usuario,' . $user->id_usuario . ',id_usuario',
            'telefono_usuario' => 'required',
            'direccion_usuario' => 'required',
            'estado' => 'required',
            'imagen' => 'nullable|image|mimes:jpg,png,jpeg,webp'
        ]);

        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('perfiles', 'public');
            $user->imagen_url_usuario = '/storage/' . $rutaImagen;
        }

        if (!empty($request->contrasena)) {
            $request->validate([
                'contrasena' => 'min:8'
            ]);
            $user->contrasena_usuario = Hash::make($request->contrasena);
        }

        $user->update($request->except(['contrasena', 'imagen']));

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /* ============================================================
     * ELIMINAR
     * ============================================================ */
    public function destroy(Usuarios $user)
    {
        $user->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado.');
    }

    /* ============================================================
     * ENVIAR CORREO PARA RESTABLECER CONTRASEÑA
     * ============================================================ */
    public function enviarEnlaceRestablecimiento(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $usuario = Usuarios::where('correo_usuario', $request->email)->first();

        if (!$usuario) {
            return back()->withErrors('Correo no registrado.');
        }

        $token = sha1(time() . $usuario->correo_usuario);

        $usuario->token_recuperacion = $token;
        $usuario->token_expiracion = Carbon::now()->addMinutes(30);
        $usuario->save();

        Mail::send('emails.recuperar', [
            'link' => route('password.reset.form', ['token' => $token, 'email' => $usuario->correo_usuario])
        ], function ($msg) use ($usuario) {
            $msg->to($usuario->correo_usuario)
                ->subject('Restablecimiento de contraseña - PROGAN');
        });

        return back()->with('success', 'Correo enviado correctamente.');
    }

    /* ============================================================
     * MOSTRAR FORMULARIO DE NUEVA CONTRASEÑA
     * ============================================================ */
    public function mostrarFormularioNuevaContrasena(Request $request)
    {
        $usuario = Usuarios::where('token_recuperacion', $request->token)->first();

        if (!$usuario || Carbon::parse($usuario->token_expiracion)->isPast()) {
            return redirect()->route('login')->withErrors('Solicitud expirada.');
        }

        return view('auth.nueva_contrasena', [
            'token' => $request->token,
            'email' => $request->email
        ]);
    }

    /* ============================================================
     * RESTABLECER CONTRASEÑA
     * ============================================================ */
    public function restablecer(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'contrasenaNueva' => 'required|min:8|same:confirmarContrasena',
        ]);

        $usuario = Usuarios::where('correo_usuario', $request->email)
            ->where('token_recuperacion', $request->token)
            ->first();

        if (!$usuario) {
            return back()->withErrors('Token inválido.');
        }

        $usuario->contrasena_usuario = Hash::make($request->contrasenaNueva);
        $usuario->token_recuperacion = null;
        $usuario->token_expiracion = null;
        $usuario->save();

        return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente.');
    }

    /* ============================================================
     * LOGOUT
     * ============================================================ */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('logout', 'ok');
    }
}
