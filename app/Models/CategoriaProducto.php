<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoriaProducto extends Model
{
    protected $table = 'categoriasproducto'; 
    protected $primaryKey = 'id_categoria';
    public $timestamps = false;

    protected $fillable = [
        'nombre_categoria',
    ];

    // Relación inversa con productos
    public function productos()
    {
        return $this->hasMany(ProductoGanadero::class, 'categoria_id', 'id_categoria');
    }
}
