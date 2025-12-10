<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios; // Ajusta el namespace si tu modelo está en otro lugar
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerfilController extends Controller
{
    // Directorio público donde se guardarán las imágenes (igual a tu estructura original)
    // Nota: este directorio debe ser accesible públicamente (public/modules/auth/perfiles)
    protected $uploadDirRelative = 'modules/auth/perfiles';
    protected $uploadDirFull; // se define en el constructor

    public function __construct()
    {
        $this->uploadDirFull = public_path($this->uploadDirRelative);
    }

    /**
     * Mostrar perfil del usuario.
     * Si no se pasa $id usa session('id_usuario').
     */
    // dentro de PerfilController
    public function listarUsuario(Request $request, $id = null)
    {
        $id = $id ?? session('id_usuario');

        if (!$id) {
            session()->flash('error', 'ID de usuario no proporcionado.');
            return redirect()->route('home.index'); // o a donde convenga
        }

        $user = Usuarios::find($id);
        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            return redirect()->route('home.index');
        }

        $userData = $user->toArray();
        return view('editarPerfil', compact('userData'));
    }


    /**
     * Actualizar perfil del usuario (action del form).
     */
    public function actualizar(Request $request)
    {
        // Intentar obtener id desde input o desde sesión
        $id = $request->input('id_usuario') ?? session('id_usuario');

        if (!$id) {
            session()->flash('error', 'Solicitud no válida para actualizar el perfil (ID no disponible).');
            return redirect()->back();
        }

        // Reglas de validación (similares a tus validaciones originales)
        $rules = [
            'nombreCompleto' => 'required|string|max:255',
            'nombre_usuario' => 'required|string|max:100',
            'correo_usuario'  => 'required|email|max:255',
            'telefono_usuario' => 'nullable|string|max:30',
            'direccion_usuario' => 'nullable|string|max:255',
            'contrasena' => [
                'nullable',
                'string',
                'min:8',
                // si quieres mantener la regex exacta:
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/'
            ],
            'fileFoto' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:5120' // max 5MB
        ];

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'correo_usuario.email' => 'Correo electrónico no válido.',
            'contrasena.min' => 'La contraseña debe tener al menos :min caracteres.',
            'contrasena.regex' => 'La contraseña debe incluir mayúscula, minúscula, número y carácter especial.',
            'fileFoto.mimes' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF.',
            'fileFoto.max' => 'La imagen no puede superar 5MB.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        // Validaciones personalizadas: si correo o usuario cambian, verificar unicidad
        $validator->after(function ($v) use ($request, $id) {
            $correo = $request->input('correo_usuario');
            $usuario = $request->input('nombre_usuario');

            if ($correo) {
                $exists = Usuarios::where('correo_usuario', $correo)
                    ->where('id_usuario', '!=', $id)
                    ->exists();
                if ($exists) {
                    $v->errors()->add('correo_usuario', 'El correo electrónico ya está en uso por otro usuario.');
                }
            }

            if ($usuario) {
                $exists2 = Usuarios::where('nombre_usuario', $usuario)
                    ->where('id_usuario', '!=', $id)
                    ->exists();
                if ($exists2) {
                    $v->errors()->add('nombre_usuario', 'El nombre de usuario ya está en uso por otro usuario.');
                }
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Iniciar transacción
        DB::beginTransaction();
        try {
            $user = Usuarios::find($id);
            if (!$user) {
                DB::rollBack();
                session()->flash('error', 'Usuario a actualizar no encontrado.');
                return redirect()->back();
            }

            // Campos
            $user->nombreCompleto = trim($request->input('nombreCompleto'));
            $user->nombre_usuario = trim($request->input('nombre_usuario'));
            $user->correo_usuario = trim($request->input('correo_usuario'));
            $user->telefono_usuario = trim($request->input('telefono_usuario'));
            $user->direccion_usuario = trim($request->input('direccion_usuario'));
            // conservar estado y rol existente
            // $user->estado = $user->estado;
            // $user->id_rol = $user->id_rol;

            // Manejo contraseña (solo si la envían)
            $nuevaPass = $request->input('contrasena');
            if (!empty($nuevaPass)) {
                $user->contrasena_usuario = Hash::make($nuevaPass);
            }

            // Manejo de imagen
            $imagen_url = $request->input('imagen_url_actual', null) ?: null;

            if ($request->hasFile('fileFoto') && $request->file('fileFoto')->isValid()) {
                $file = $request->file('fileFoto');
                $extension = strtolower($file->getClientOriginalExtension());
                $allowed = ['jpg','jpeg','png','gif'];
                if (!in_array($extension, $allowed)) {
                    throw new \Exception('Tipo de archivo de imagen no permitido. Solo JPG, JPEG, PNG, GIF.');
                }

                // Crear directorio si no existe
                if (!is_dir($this->uploadDirFull)) {
                    mkdir($this->uploadDirFull, 0777, true);
                }

                $newFileName = md5(time() . $file->getClientOriginalName()) . '.' . $extension;
                $destPath = $this->uploadDirFull . DIRECTORY_SEPARATOR . $newFileName;

                // Mover archivo
                $moved = $file->move($this->uploadDirFull, $newFileName);
                if (!$moved) {
                    throw new \Exception('Error al mover la nueva imagen subida al servidor.');
                }

                // Construir URL pública (ajusta si tu base URL difiere)
                $imagen_url = asset($this->uploadDirRelative . '/' . $newFileName);
                $user->imagen_url_usuario = $imagen_url;
            } else {
                // Si no sube nueva imagen, mantener la actual (ya en $user->imagen_url_usuario)
                if ($imagen_url) {
                    $user->imagen_url_usuario = $imagen_url;
                }
            }

            // Guardar fecha de actualización si lo deseas
            $user->fecha_actualizacion = Carbon::now()->toDateTimeString();

            $user->save();

            DB::commit();

            // Actualizar sesión para reflejar los cambios (si tu app usa estas claves)
            session([
                'url_Usuario' => $user->imagen_url_usuario,
                'nombre' => $user->nombreCompleto,
                'usuario' => $user->nombre_usuario,
                'correo_usuario' => $user->correo_usuario,
                'id_usuario' => $user->id_usuario,
            ]);

            session()->flash('message', 'Perfil actualizado correctamente.');

            // Redirigir a listarUsuario
            return redirect()->route('perfil.listarUsuario', ['id' => $user->id_usuario]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error actualizando perfil: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Enviar mensaje de error a la vista
            session()->flash('error', 'Error al actualizar: ' . $e->getMessage());

            return redirect()->back()->withInput();
        }
    }
}
