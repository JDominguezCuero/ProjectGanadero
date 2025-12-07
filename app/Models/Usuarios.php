<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuarios extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // cambiar a true si tienes created_at/updated_at

    protected $fillable = [
        'nombreCompleto',
        'nombre_usuario',
        'correo_usuario',
        'direccion_usuario',
        'imagen_url_usuario',
        'estado',
        'contrasena_usuario',
        'telefono_usuario',
        'id_rol',
        'token_expiracion',
        'token_recuperacion',
        'fecha_creacion',
        'fecha_ultimo_acceso',
    ];

    protected $hidden = [
        'contrasena_usuario',
        'token_recuperacion',
    ];

    /**
     * Laravel usará este método para leer el hash de la contraseña.
     */
    public function getAuthPassword()
    {
        return $this->contrasena_usuario;
    }

    /**
     * Si Laravel internamente hace $user->password = '...', redirigimos esa
     * asignación a la columna 'contrasena_usuario' para evitar que intente escribir
     * en una columna 'password' inexistente.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['contrasena_usuario'] = $value;
    }

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }

    public function productos()
    {
        return $this->hasMany(ProductoGanadero::class, 'id_usuario', 'id_usuario');
    }
}
