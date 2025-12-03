<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventarioAlimentos extends Model
{
    protected $table = 'inventarioalimentos';
    protected $primaryKey = 'id_alimento';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'cantidad',
        'unidad_medida',
        'fecha_ingreso',
    ];
}
