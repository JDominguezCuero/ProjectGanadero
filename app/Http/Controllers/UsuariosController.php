<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;

class UsuariosController extends Controller
{
    public function index(Request $request)
    {
        // Por ahora solo cargamos un usuario de ejemplo
        $id = $request->input('id_usuario', 1); // id 1 de prueba
        $userData = Usuarios::find($id);

        return view('index', compact('userData'));
    }

    public function actualizar(Request $request)
    {
        // Aquí iría la lógica de actualizar el usuario
        return redirect()->back()->with('message', 'Perfil actualizado correctamente');
    }
    
}
