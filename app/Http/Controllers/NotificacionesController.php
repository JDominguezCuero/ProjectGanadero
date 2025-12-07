<?php

namespace App\Http\Controllers;

use App\Models\Notificacion; // Asegúrate de que esta sea la clase correcta
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class NotificacionesController extends Controller
{
    /** ----------------------------------------------
     *  LISTAR NOTIFICACIONES (vista)
     * ----------------------------------------------*/
    public function index()
    {
        $userId = Auth::id();

        // CAMBIAR "Notification::" por "Notificacion::"
        $notificaciones = Notificacion::where('id_usuario_receptor', $userId)
            ->orderBy('fecha', 'desc')
            ->get();

        return view('notificaciones.index', compact('notificaciones'));
    }

    /** ----------------------------------------------
     *  LISTAR NOTIFICACIONES (AJAX JSON)
     * ----------------------------------------------*/
    public function listarNotificaciones()
    {
        $userId = Auth::id();

        $notificaciones = Notificacion::where('id_usuario_receptor', $userId)
            ->orderBy('fecha', 'desc')
            ->get();

        return response()->json($notificaciones);
    }

    /** ----------------------------------------------
     *  INSERTAR NOTIFICACIÓN DE INTERÉS
     * ----------------------------------------------*/
    public function store(Request $request)
    {
        $request->validate([
            'id_vendedor' => 'required',
            'id_producto' => 'required',
            'mensaje'     => 'nullable|string'
        ]);

        $notificacion = Notificacion::create([
            'id_usuario_receptor' => $request->id_vendedor,
            'id_usuario_emisor'   => Auth::id(),
            'id_producto'         => $request->id_producto,
            'mensaje'             => $request->mensaje ?? "Estoy interesado en tu producto.",
            'tipo'                => 'interes',
            'leida'               => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notificación enviada.',
            'data'    => $notificacion
        ]);
    }

    /** ----------------------------------------------
     *  MARCAR COMO LEÍDO (varios)
     * ----------------------------------------------*/
    public function marcarComoLeido(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        Notificacion::whereIn('id', $request->ids)
            ->update(['leida' => 1]);

        return response()->json([
            'success' => true,
            'message' => 'Notificaciones marcadas como leídas.'
        ]);
    }

    /** ----------------------------------------------
     *  ELIMINAR SELECCIONADAS
     * ----------------------------------------------*/
    public function eliminarSeleccionadas(Request $request)
    {
        $request->validate([
            'ids' => 'required|array'
        ]);

        Notificacion::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificaciones eliminadas.'
        ]);
    }

    /** ----------------------------------------------
     *  ELIMINAR TODAS
     * ----------------------------------------------*/
    public function eliminarTodas()
    {
        $userId = Auth::id();

        Notificacion::where('id_usuario_receptor', $userId)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones eliminadas.'
        ]);
    }

    /** ----------------------------------------------
     *  RESPUESTA RÁPIDA (EMAIL + NOTIFICACIÓN INTERNA)
     * ----------------------------------------------*/
    public function enviarRespuestaRapida(Request $request)
    {
        $request->validate([
            'destinatarioEmail'   => 'required|email',
            'destinatarioNombre'  => 'required',
            'destinatarioId'      => 'required|integer',
            'destinatarioTelefono'=> 'nullable|string',
            'mensaje'             => 'required|string',
            'nombreProducto'      => 'required|string',
            'idProducto'          => 'required|integer'
        ]);

        $vendedor = Auth::user();

        // ---- Enviar correo ----
        $email = new PHPMailer(true);
        $exito = true;
        $mensajes = [];

        try {
            $email->isSMTP();
            $email->Host = 'smtp.gmail.com';
            $email->SMTPAuth = true;
            $email->Username = 'jsdmngzc@gmail.com';
            $email->Password = 'uhcj wqsm ntvy ixxr';
            $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $email->Port = 465;

            $email->setFrom($vendedor->correo_usuario, $vendedor->nombreCompleto);
            $email->addAddress($request->destinatarioEmail, $request->destinatarioNombre);

            $email->isHTML(true);
            $email->Subject = "Respuesta sobre {$request->nombreProducto}";
            $email->Body = "
                <p>Hola <strong>{$request->destinatarioNombre}</strong>,</p>
                <p>{$vendedor->nombreCompleto} respondió:</p>
                <blockquote>{$request->mensaje}</blockquote>
                <p>Producto: <strong>{$request->nombreProducto}</strong></p>
            ";

            $email->send();
            $mensajes[] = "Correo enviado.";
        } catch (Exception $e) {
            $exito = false;
            $mensajes[] = "Fallo al enviar correo.";
        }

        // ---- Crear notificación interna ----
        $notificacion = Notificacion::create([
            'id_usuario_receptor' => $request->destinatarioId,
            'id_usuario_emisor'   => Auth::id(),
            'id_producto'         => $request->idProducto,
            'mensaje'             => "Respuesta: {$request->mensaje}",
            'tipo'                => 'respuesta',
            'leida'               => 0,
        ]);

        if ($notificacion) {
            $mensajes[] = "Notificación interna creada.";
        } else {
            $exito = false;
            $mensajes[] = "Fallo al crear notificación interna.";
        }

        // ---- SMS Simulado ----
        if (!empty($request->destinatarioTelefono)) {
            $mensajes[] = "SMS simulado enviado.";
        }

        return response()->json([
            'success' => $exito,
            'message' => implode(" ", $mensajes)
        ]);
    }
}
