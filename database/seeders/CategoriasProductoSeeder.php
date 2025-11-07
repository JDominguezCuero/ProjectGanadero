<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categorias_producto')->insert([
            ['id_categoria' => 5, 'nombre_categoria' => 'Alimentos para Animales'],
            ['id_categoria' => 4, 'nombre_categoria' => 'Carnes y Embutidos'],
            ['id_categoria' => 6, 'nombre_categoria' => 'Equipos y Suministros'],
            ['id_categoria' => 3, 'nombre_categoria' => 'Lácteos y Huevos'],
            ['id_categoria' => 1, 'nombre_categoria' => 'Ofertas'],
            ['id_categoria' => 2, 'nombre_categoria' => 'Productos Frescos'],
        ]);
    }
}
