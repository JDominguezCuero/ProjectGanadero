<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Usuarios;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


class UsuariosController extends Controller
{
    // LISTAR
    public function listar(Request $request)
    {
        $usuarios = Usuarios::with('rol')->paginate(20);
        $roles = DB::table('roles')->get();
        $msg = $request->query('msg', null);

        return view('auth.gestionUsuario', compact('usuarios', 'roles', 'msg'));
    }

    // LOGIN
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                // Validación básica
                $request->validate([
                    'correoElectronicoLogin' => 'required|email',
                    'contrasenaLogin' => 'required|string',
                ]);

                $correo = trim($request->input('correoElectronicoLogin'));
                $contrasena = $request->input('contrasenaLogin');

                // Intentar autenticación. IMPORTANTE: en config/auth.php el provider 'users'
                // debe apuntar a App\Models\Usuarios::class (lo hiciste).
                $credentials = [
                    'correo_usuario' => $correo,
                    'password' => $contrasena, // Auth::attempt usa 'password' por convención
                ];

                if (Auth::attempt($credentials, $request->filled('remember'))) {
                    $usuario = Auth::user();

                    // Verificar estado
                    if ($usuario->estado !== 'Activo') {
                        Auth::logout();
                        return redirect()->route('autenticacion')->with([
                            'login' => 1,
                            'error' => 'El usuario no se encuentra activo, por favor contactese con el administrador'
                        ]);
                    }

                    // Regenerar sesión por seguridad
                    $request->session()->regenerate();

                    // Guardar datos que necesites en sesión
                    session([
                        'usuario' => $usuario->nombre_usuario,
                        'nombre' => $usuario->nombreCompleto,
                        'id_usuario' => $usuario->id_usuario,
                        'correo_usuario' => $usuario->correo_usuario,
                        'rol' => $usuario->id_rol,
                        'url_Usuario' => $usuario->imagen_url_Usuario,
                    ]);

                    return redirect()->route('home.index');
                }

                // Credenciales inválidas
                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'error' => 'Usuario o contraseña incorrecta'
                ]);
            } catch (\Illuminate\Validation\ValidationException $ve) {
                // Errores de validación -> regresar con mensaje
                $msg = implode(' ', $ve->errors()->flatten()->toArray());
                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'error' => $msg
                ])->withInput();
            } catch (\Exception $e) {
                \Log::error('Error login: ' . $e->getMessage());
                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'error' => 'Error en el proceso de login. Intente de nuevo.'
                ]);
            }
        }

        // GET -> vista de autenticación
        return view('usuarios.autenticacion');
    }

    // REGISTRO (adaptado al estilo del login)
    // REGISTRO (compatible con tu formulario: nombreCompleto, correoElectronico, usuario, contrasena)
    public function registro(Request $request)
    {
        if ($request->isMethod('post')) {
            // Reglas básicas
            $rules = [
                'nombreCompleto'    => 'required|string|max:255',
                'correoElectronico' => 'required|email|max:255|unique:usuarios,correo_usuario',
                'usuario'           => 'required|string|max:100|unique:usuarios,nombre_usuario',
                'contrasena'        => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'
                ],
            ];

            $messages = [
                'required' => 'El campo :attribute es obligatorio.',
                'correoElectronico.email' => 'Correo electrónico no válido.',
                'correoElectronico.unique' => 'El correo electrónico ya está registrado.',
                'usuario.unique' => 'El nombre de usuario ya está en uso.',
                'contrasena.min' => 'La contraseña debe tener al menos :min caracteres.',
                'contrasena.regex' => 'La contraseña debe incluir mayúscula, minúscula, número y carácter especial.'
            ];

            try {
                $request->validate($rules, $messages);

                $nombre = trim($request->input('nombreCompleto'));
                $correo = trim($request->input('correoElectronico'));
                $usuario = trim($request->input('usuario'));
                $contrasena = $request->input('contrasena');
                $imagen_url = config('app.url') . '/images/profileDefault.png';

                // Iniciar transacción para detectar errores a nivel BD
                DB::beginTransaction();

                $nuevo = new Usuarios();
                $nuevo->nombreCompleto = $nombre;
                $nuevo->correo_usuario = $correo;
                $nuevo->nombre_usuario = $usuario;
                $nuevo->contrasena_usuario = Hash::make($contrasena);
                $nuevo->imagen_url_usuario = $imagen_url;
                $nuevo->estado = 'Activo';
                $nuevo->fecha_creacion = Carbon::now()->toDateTimeString();
                $nuevo->save();

                DB::commit();

                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'success' => 'Registro exitoso. Inicia sesión.'
                ]);
            } catch (QueryException $qe) {
                DB::rollBack();
                // Info detallada para el log (no se muestra al usuario)
                Log::error('QueryException en registro usuario: ' . $qe->getMessage(), [
                    'code' => $qe->getCode(),
                    'sql' => $qe->getSql(),
                    'bindings' => $qe->getBindings()
                ]);
                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'error' => 'Ocurrió un error al intentar registrar el usuario. Intente de nuevo.'
                ])->withInput();
            } catch (\Illuminate\Validation\ValidationException $ve) {
                $msg = implode(' ', $ve->errors()->flatten()->toArray());
                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'error' => $msg
                ])->withInput();
            } catch (\Exception $e) {
                DB::rollBack();
                // Log completo: mensaje y stack trace
                Log::error('Excepción en registro usuario: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                return redirect()->route('autenticacion')->with([
                    'login' => 1,
                    'error' => 'Ocurrió un error al intentar registrar el usuario. Intente de nuevo.'
                ])->withInput();
            }
        }

        return view('usuarios.autenticacion');
    }


    // AGREGAR
    public function agregar(Request $request)
    {
        if (!$request->isMethod('post')) {
            return redirect()->route('usuarios.list')->with('inv', 1)->withErrors(['error' => 'Método no permitido.']);
        }

        try {
            $mensjError = '';

            $nombre = $request->input('nombreCompleto', '');
            $usuario = $request->input('nombre_usuario', '');
            $correo = $request->input('correo_usuario', '');
            $telefono = $request->input('telefono_usuario', '');
            $rol_id = $request->input('rol_id', '');
            $direccion = $request->input('direccion_usuario', '');
            $estado = $request->input('estado', '');
            $contrasena = $request->input('contrasena', '');
            $imagen_url = '';

            if (!$this->camposNoVacios([$nombre, $usuario, $correo, $telefono, $rol_id, $direccion, $estado, $contrasena])) {
                $mensjError = "Todos los campos son obligatorios.";
                throw new \Exception($mensjError);
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $mensjError = "Correo electrónico no válido.";
                throw new \Exception($mensjError);
            }
            if (!$this->validarPassword($contrasena, 5)) {
                $mensjError = "La contraseña debe tener mínimo 5 caracteres, una mayúscula, una minúscula, un número y un carácter especial.";
                throw new \Exception($mensjError);
            }
            if (Usuarios::where('correo_usuario', $correo)->exists()) {
                $mensjError = "El correo electrónico ya está registrado.";
                throw new \Exception($mensjError);
            }

            if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                $file = $request->file('imagen');
                $allowed = ['jpg','jpeg','png','gif'];
                $ext = strtolower($file->getClientOriginalExtension());
                if (!in_array($ext, $allowed)) {
                    $mensjError = "Tipo de archivo de imagen no permitido.";
                } else {
                    $newFileName = md5(time().$file->getClientOriginalName()).'.'.$ext;
                    $path = $file->storeAs('public/perfiles', $newFileName);
                    $imagen_url = Storage::url($path);
                }
            }

            if (!empty($mensjError)) {
                return redirect()->route('usuarios.list')->with('inv',1)->withErrors(['error' => $mensjError]);
            }

            if (empty($imagen_url)) {
                $imagen_url = config('app.url') . '/modules/auth/perfiles/profileDefault.png';
            }

            $nuevo = new Usuarios();
            $nuevo->nombreCompleto = $nombre;
            $nuevo->nombre_usuario = $usuario;
            $nuevo->correo_usuario = $correo;
            $nuevo->telefono_usuario = $telefono;
            $nuevo->id_rol = $rol_id;
            $nuevo->direccion_usuario = $direccion;
            $nuevo->estado = $estado;
            $nuevo->contrasena_usuario = Hash::make($contrasena);
            $nuevo->imagen_url_Usuario = $imagen_url;
            $nuevo->fecha_creacion = Carbon::now()->toDateTimeString();
            $nuevo->save();

            session(['url_Usuario' => $imagen_url]);

            $mensaje = "Usuario registrado correctamente.";
            return redirect()->route('usuarios.list', ['msg' => $mensaje]);
        } catch (\Exception $e) {
            return redirect()->route('usuarios.list')->with('inv',1)->withErrors(['error' => $e->getMessage()]);
        }
    }

    // EDITAR
    public function editar(Request $request)
    {
        $id = $request->input('id_usuario', $request->query('id_usuario', null));

        if ($request->isMethod('post') && $id) {
            try {
                $mensjError = '';

                $nombre = $request->input('nombreCompleto', '');
                $usuario = $request->input('nombre_usuario', '');
                $correo = $request->input('correo_usuario', '');
                $telefono = $request->input('telefono_usuario', '');
                $rol_id = $request->input('rol_id', '');
                $direccion = $request->input('direccion_usuario', '');
                $estado = $request->input('estado', '');
                $contrasena = $request->input('contrasena', '');
                $imagen_url = $request->input('imagen_url_actual', '');

                if (!$this->camposNoVacios([$nombre, $usuario, $correo, $telefono, $direccion, $estado])) {
                    throw new \Exception("Todos los campos son obligatorios.");
                }
                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Correo electrónico no válido.");
                }
                if (!empty($contrasena) && !$this->validarPassword($contrasena, 5)) {
                    throw new \Exception("La contraseña debe tener mínimo 5 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
                }

                if ($request->hasFile('imagen') && $request->file('imagen')->isValid()) {
                    $file = $request->file('imagen');
                    $allowed = ['jpg','jpeg','png','gif'];
                    $ext = strtolower($file->getClientOriginalExtension());
                    if (!in_array($ext, $allowed)) {
                        throw new \Exception("Tipo de archivo no permitido para la imagen.");
                    }
                    $newFileName = md5(time().$file->getClientOriginalName()).'.'.$ext;
                    $path = $file->storeAs('public/perfiles', $newFileName);
                    $imagen_url = Storage::url($path);
                }

                if (empty($imagen_url)) {
                    $imagen_url = config('app.url') . '/modules/auth/perfiles/profileDefault.png';
                }

                $usuarioModel = Usuarios::findOrFail($id);
                $usuarioModel->nombreCompleto = $nombre;
                $usuarioModel->nombre_usuario = $usuario;
                $usuarioModel->correo_usuario = $correo;
                $usuarioModel->telefono_usuario = $telefono;
                $usuarioModel->direccion_usuario = $direccion;
                $usuarioModel->estado = $estado;
                $usuarioModel->id_rol = $rol_id;
                $usuarioModel->imagen_url_Usuario = $imagen_url;
                if (!empty($contrasena)) {
                    $usuarioModel->contrasena_usuario = Hash::make($contrasena);
                }
                $usuarioModel->fecha_ultimo_acceso = Carbon::now()->toDateTimeString();
                $usuarioModel->save();

                session(['url_Usuario' => $imagen_url]);

                $mensaje = "Usuario actualizado correctamente.";
                return redirect()->route('usuarios.list', ['msg' => $mensaje]);
            } catch (\Exception $e) {
                return redirect()->route('usuarios.list')->with('inv',1)->withErrors(['error' => $e->getMessage()]);
            }
        } else if ($id) {
            $item = Usuarios::findOrFail($id);
            $roles = DB::table('roles')->get();
            return view('auth.usuarios', compact('item', 'roles'));
        } else {
            $mensjError = "ID de usuario no proporcionado.";
            return redirect()->route('usuarios.list')->with('inv',1)->withErrors(['error' => $mensjError]);
        }
    }

    // ELIMINAR
    public function eliminar(Request $request)
    {
        $id = $request->query('id', null);
        if (!$id) {
            return redirect()->route('usuarios.list')->with('inv',1)->withErrors(['error' => 'ID del usuario no proporcionado para eliminar.']);
        }

        try {
            $usuario = Usuarios::findOrFail(intval($id));
            $usuario->delete();

            $mensaje = "Usuario eliminado correctamente.";
            return redirect()->route('usuarios.list', ['msg' => $mensaje]);
        } catch (\Exception $e) {
            return redirect()->route('usuarios.list')->with('inv',1)->withErrors(['error' => 'Error al eliminar el usuario.']);
        }
    }

    // RESTABLECER
    public function restablecer(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $token = $request->input('token', '');
                $correo = $request->input('email', '');
                $nuevaContrasena = $request->input('contrasenaNueva', '');
                $confirmarContrasena = $request->input('confirmarContrasena', '');
                $mensjError = "";

                if ($nuevaContrasena !== $confirmarContrasena) {
                    $mensjError = "Las contraseñas deben coincidir";
                    throw new \Exception($mensjError);
                }
                if (!$this->camposNoVacios([$nuevaContrasena])) {
                    $mensjError = "Todos los campos son obligatorios.";
                    throw new \Exception($mensjError);
                }
                if (!$this->validarPassword($nuevaContrasena, 8)) {
                    $mensjError = "La contraseña debe tener mínimo 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.";
                    throw new \Exception($mensjError);
                }

                $usuario = Usuarios::where('correo_usuario', $correo)->first();
                if (!$usuario) {
                    $mensjError = "El correo electrónico no está registrado.";
                    throw new \Exception($mensjError);
                }

                if (!$this->tokenEsValido($token)) {
                    $mensjError = "Token inválido o expirado.";
                    throw new \Exception($mensjError);
                }

                $usuario->contrasena_usuario = Hash::make($nuevaContrasena);
                $usuario->token_recuperacion = null;
                $usuario->token_expiracion = null;
                $usuario->save();

                return redirect()->route('auth.reset.form')->with('success',1);
            } catch (\Exception $e) {
                return redirect()->route('auth.reset.form')->with([
                    'success' => 2,
                    'error' => $mensjError,
                    'token' => $request->input('token'),
                    'email' => $request->input('email')
                ]);
            }
        } else {
            return view('auth.nueva_contrasena');
        }
    }

    // enviarEnlaceRestablecimiento
    public function enviarEnlaceRestablecimiento(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $correo = $request->input('email', '');

                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    $mensjError = "Correo inválido.";
                    throw new \Exception($mensjError);
                }

                $usuario = Usuarios::where('correo_usuario', $correo)->first();
                if (!$usuario) {
                    $mensjError = "Correo no registrado.";
                    throw new \Exception($mensjError);
                }

                $token = Str::random(64);
                $usuario->token_recuperacion = $token;
                $usuario->token_expiracion = Carbon::now()->addHours(2)->toDateTimeString();
                $usuario->save();

                if ($this->enviarCorreoRestablecimiento($correo, $token)) {
                    return redirect()->route('auth.login')->with('enviado', 1);
                } else {
                    $mensjError = "No se pudo enviar el correo.";
                    throw new \Exception($mensjError);
                }
            } catch (\Exception $e) {
                return redirect()->route('auth.login')->with('enviado', 2)->withErrors(['error' => $e->getMessage() ?? $mensjError]);
            }
        } else {
            return view('usuarios.autenticacion');
        }
    }

    // mostrarFormularioNuevaContrasena
    public function mostrarFormularioNuevaContrasena(Request $request)
    {
        $token = $request->query('token', '');
        $email = $request->query('email', '');

        if (!$this->tokenEsValido($token)) {
            return redirect()->route('auth.login')->with('enviado', 2)->withErrors(['error' => 'Solicitud Expirada']);
        }

        return view('auth.nueva_contrasena', compact('token', 'email'));
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home.index');
    }

    /* helpers */
    private function camposNoVacios(array $arr): bool
    {
        foreach ($arr as $v) {
            if ($v === null || trim((string)$v) === '') return false;
        }
        return true;
    }

    private function validarPassword(string $pass, int $minLength = 8): bool
    {
        if (strlen($pass) < $minLength) return false;
        if (!preg_match('/[A-Z]/', $pass)) return false;
        if (!preg_match('/[a-z]/', $pass)) return false;
        if (!preg_match('/[0-9]/', $pass)) return false;
        if (!preg_match('/[\W]/', $pass)) return false;
        return true;
    }

    private function enviarCorreoRestablecimiento(string $correo, string $token): bool
    {
        try {
            $link = url('/auth/mostrarFormularioNuevaContrasena') . '?token=' . $token . '&email=' . urlencode($correo);

            Mail::send('emails.reset', ['link' => $link], function ($message) use ($correo) {
                $message->to($correo);
                $message->from(config('mail.from.address'), config('mail.from.name'));
                $message->subject('Restablecimiento de cuenta - PROGAN');
            });

            return count(Mail::failures()) === 0;
        } catch (\Exception $e) {
            \Log::error('Error al enviar correo: ' . $e->getMessage());
            return false;
        }
    }

    private function tokenEsValido(?string $token): bool
    {
        if (empty($token)) return false;
        $row = DB::table('usuarios')->select('correo_usuario', 'token_expiracion')->where('token_recuperacion', $token)->first();
        if (!$row) return false;

        $fechaExp = strtotime($row->token_expiracion);
        $ahora = time();
        return $fechaExp > $ahora;
    }
}
