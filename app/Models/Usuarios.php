<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false; // Tu tabla no usa created_at/updated_at

    protected $fillable = [
        'nombreCompleto',
        'nombre_usuario',
        'correo_usuario',
        'direccion_usuario',
        'estado',
        'contrasena_usuario',
        'telefono_usuario',
        'id_rol',
        'imagen_url_Usuario',
        'token_expiracion',
        'token_recuperacion',
        'fecha_creacion',
        'fecha_ultimo_acceso',
    ];

    /**
     * Relación: un usuario pertenece a un rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function productos()
    {
        return $this->hasMany(ProductoGanadero::class, 'id_usuario', 'id_usuario');
    }


}
