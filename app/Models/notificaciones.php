<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Notificacion extends Model
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario_receptor',
        'id_usuario_emisor',
        'id_producto',
        'mensaje',
        'fecha',
        'leido',
        'tipo_notificacion'
    ];

    /* =======================================================
     *  RELACIONES
     * =======================================================*/

    // Producto relacionado
    public function producto()
    {
        return $this->belongsTo(ProductoGanadero::class, 'id_producto', 'id_producto');
    }

    // Usuario emisor
    public function emisor()
    {
        return $this->belongsTo(User::class, 'id_usuario_emisor', 'id_usuario');
    }

    /* =======================================================
     *  MÉTODOS EQUIVALENTES A TU MODELO PHP
     * =======================================================*/

    /**
     * Obtener notificaciones de un usuario con datos completos.
     */
    public static function obtenerPorUsuario($id_usuario_receptor)
    {
        return DB::table('notificaciones as n')
            ->leftJoin('productosganaderos as pg', 'n.id_producto', '=', 'pg.id_producto')
            ->leftJoin('usuarios as u', 'n.id_usuario_emisor', '=', 'u.id_usuario')
            ->where('n.id_usuario_receptor', $id_usuario_receptor)
            ->orderBy('n.fecha', 'desc')
            ->select(
                'n.id as id_notificacion',
                'n.id_usuario_emisor',
                'n.id_usuario_receptor',
                'n.id_producto',
                'n.mensaje',
                'n.leido',
                'n.fecha',
                'n.tipo_notificacion',

                'pg.nombre_producto',
                'pg.descripcion_producto',
                'pg.imagen_url',
                'pg.precio_unitario',

                'u.id_usuario as id_usuario_vendedor',
                'u.nombreCompleto as emisor_nombre',
                'u.correo_usuario as emisor_correo',
                'u.telefono_usuario as emisor_telefono'
            )
            ->get();
    }

    /**
     * Marcar como leídas varias notificaciones.
     */
    public static function marcarComoLeido(array $ids)
    {
        return self::whereIn('id', $ids)->update(['leido' => 1]);
    }

    /**
     * Eliminar varias notificaciones.
     */
    public static function eliminarVarias(array $ids)
    {
        return self::whereIn('id', $ids)->delete();
    }

    /**
     * Eliminar todas las notificaciones de un usuario.
     */
    public static function eliminarTodasUsuario($id_usuario_receptor)
    {
        return self::where('id_usuario_receptor', $id_usuario_receptor)->delete();
    }

    /**
     * Insertar una nueva notificación.
     */
    public static function insertar($id_usuario_receptor, $id_emisor, $id_producto, $mensaje, $tipo = 'interes')
    {
        return self::create([
            'id_usuario_receptor' => $id_usuario_receptor,
            'id_usuario_emisor'   => $id_emisor,
            'id_producto'         => $id_producto,
            'mensaje'             => $mensaje,
            'fecha'               => now(),
            'leido'               => 0,
            'tipo_notificacion'   => $tipo
        ]);
    }
}
