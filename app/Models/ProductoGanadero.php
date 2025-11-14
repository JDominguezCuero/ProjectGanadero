<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoGanadero extends Model
{
    use HasFactory;

    protected $table = 'productosganaderos'; 
    protected $primaryKey = 'id_producto';
    public $timestamps = false; // si tu tabla no tiene created_at / updated_at

    protected $fillable = [
        'nombre_producto',
        'descripcion_producto',
        'categoria_id',
        'cantidad',
        'estado_oferta',
        'fecha_publicacion',
        'id_usuario',
        'imagen_url',
        'precio_anterior',
        'precio_unitario',
    ];

    // Relación con la categoría
    public function categoria()
    {
        return $this->belongsTo(CategoriaProducto::class, 'categoria_id', 'id_categoria');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }

}
