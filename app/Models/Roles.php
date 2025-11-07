<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class roles extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'nombre_rol',
        'descripcion',
    ];

    // Relación inversa: un rol tiene muchos usuarios
    // La relación en Usuario (belongsTo) es la importante para tu flujo normal.
    // La relación en Rol (hasMany) es opcional, solo sirve si alguna vez necesitas traer todos los usuarios de un rol.

    // belongsTo significa: "este usuario pertenece a un rol".
    // hasMany significa: "este rol tiene muchos usuarios".

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'id_rol', 'id_rol');
    }
}
