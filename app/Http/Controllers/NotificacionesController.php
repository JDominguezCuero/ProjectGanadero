<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\Notificacion;

class NotificacionesController extends Controller
{
    /* ============================================================
     * LISTAR NOTIFICACIONES (vista)
     * ============================================================*/
    public function index()
    {
        $userId = Auth::id();

        $notificaciones = Notificacion::obtenerPorUsuario($userId);

        return view('notificaciones.notificaciones', compact('notificaciones'));
    }

    /* ============================================================
     * LISTAR NOTIFICACIONES (JSON)
     * ============================================================*/
    public function listarJson()
    {
        return response()->json(
            Notificacion::obtenerPorUsuario(Auth::id())
        );
    }

    /* ============================================================
     * INSERTAR NOTIFICACIÓN DE INTERÉS
     * ============================================================*/
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_vendedor' => 'required|integer',
            'id_producto' => 'required|integer',
            'mensaje'     => 'nullable|string'
        ]);

        $mensaje = $validated['mensaje'] ?? "Estoy interesado en tu producto.";

        $notificacion = Notificacion::insertar(
            $validated['id_vendedor'],
            Auth::id(),
            $validated['id_producto'],
            $mensaje,
            'interes'
        );

        if ($notificacion) {
            return response()->json(['success' => true, 'message' => 'Notificación enviada']);
        }

        return response()->json(['success' => false, 'message' => 'Error al crear notificación'], 500);
    }

    /* ============================================================
     * MARCAR VARIAS NOTIFICACIONES COMO LEÍDAS
     * ============================================================*/
    public function marcarComoLeido(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'IDs inválidos']);
        }

        Notificacion::marcarComoLeido($ids);

        return response()->json(['success' => true, 'message' => 'Notificaciones marcadas como leídas']);
    }

    /* ============================================================
     * ELIMINAR VARIAS NOTIFICACIONES
     * ============================================================*/
    public function eliminarVarias(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'IDs inválidos']);
        }

        Notificacion::eliminarVarias($ids);

        return response()->json(['success' => true, 'message' => 'Notificaciones eliminadas']);
    }

    /* ============================================================
     * ELIMINAR TODAS LAS NOTIFICACIONES DE UN USUARIO
     * ============================================================*/
    public function eliminarTodas()
    {
        Notificacion::eliminarTodasUsuario(Auth::id());

        return response()->json(['success' => true, 'message' => 'Todas las notificaciones eliminadas']);
    }

    /* ============================================================
     * ENVIAR RESPUESTA RÁPIDA (Correo + notificación interna)
     * ============================================================*/
    public function enviarRespuestaRapida(Request $request)
    {
        $validated = $request->validate([
            'destinatarioEmail'   => 'required|email',
            'destinatarioNombre'  => 'required|string',
            'destinatarioId'      => 'required|integer',
            'destinatarioTelefono'=> 'nullable|string',
            'mensaje'             => 'required|string',
            'nombreProducto'      => 'required|string',
            'idProducto'          => 'required|integer'
        ]);

        $vendedor = User::find(Auth::id());

        /* =====================================================
         * 1. Enviar correo
         * =====================================================*/
        try {
            Mail::send([], [], function ($message) use ($validated, $vendedor) {
                $message->to($validated['destinatarioEmail'])
                    ->subject("Respuesta de {$vendedor->nombreCompleto} sobre tu interés")
                    ->setBody("
                        <h3>Respuesta del vendedor</h3>
                        <p>{$validated['mensaje']}</p>
                        <p>Producto: {$validated['nombreProducto']}</p>
                    ", 'text/html');
            });
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al enviar correo']);
        }

        /* =====================================================
         * 2. Crear notificación interna
         * =====================================================*/
        Notificacion::insertar(
            $validated['destinatarioId'],
            Auth::id(),
            $validated['idProducto'],
            "Respuesta: {$validated['mensaje']}",
            'respuesta'
        );

        /* =====================================================
         * 3. SMS SIMULADO
         * =====================================================*/
        if (!empty($validated['destinatarioTelefono'])) {
            \Log::info("SMS SIMULADO a {$validated['destinatarioTelefono']}: {$validated['mensaje']}");
        }

        return response()->json([
            'success' => true,
            'message' => 'Respuesta enviada correctamente (Correo + Notificación interna)'
        ]);
    }
}
