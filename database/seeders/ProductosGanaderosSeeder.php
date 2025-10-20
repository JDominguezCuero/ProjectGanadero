<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosGanaderosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            
        DB::table('productosganaderos')->insert([
            [
                'id_producto' => 7,
                'nombre_producto' => 'Alimento Concentrado Premium para Cerdos',
                'descripcion_producto' => 'Bolsa de 25 kg de concentrado balanceado para cerdos en etapa de engorde. Contiene proteínas, vitaminas y minerales esenciales.',
                'categoria_id' => 5, // Alimentos para Animales
                'cantidad' => 75,
                'estado_oferta' => 0,
                'fecha_publicacion' => '2025-06-02 22:04:54',
                'id_usuario' => 1,
                'imagen_url' => '/ProjectGanadero/public/images/productos/8a230990fde9dcc06e0576fcf4d398ed.jpg',
                'precio_anterior' => null,
                'precio_unitario' => 25.00,
            ],
            [
                'id_producto' => 8,
                'nombre_producto' => 'Peras Frescas',
                'descripcion_producto' => 'Caja de peras frescas seleccionadas, de excelente calidad, ideales para consumo directo o preparación de postres.',
                'categoria_id' => 2, // Productos Frescos
                'cantidad' => 20,
                'estado_oferta' => 1,
                'fecha_publicacion' => '2025-06-02 22:24:00',
                'id_usuario' => 1,
                'imagen_url' => '/ProjectGanadero/public/images/productos/04e9b5d6feff0d783fad3a6e3278bb1a.jpg',
                'precio_anterior' => 3000.00,
                'precio_unitario' => 2500.00,
            ],
            [
                'id_producto' => 9,
                'nombre_producto' => 'Mangos Dulces',
                'descripcion_producto' => 'Mangos maduros de primera calidad, jugosos y dulces, perfectos para jugos, postres o consumo directo.',
                'categoria_id' => 2, // Productos Frescos
                'cantidad' => 30,
                'estado_oferta' => 1,
                'fecha_publicacion' => '2025-06-02 23:43:44',
                'id_usuario' => 1,
                'imagen_url' => '/ProjectGanadero/public/images/productos/ca06ea4e8bc3c3ed4cdc994f5a5bec3f.png',
                'precio_anterior' => 3000.00,
                'precio_unitario' => 2500.00,
            ],
            // Productos adicionales para más realismo:
            [
                'id_producto' => 10,
                'nombre_producto' => 'Leche Fresca Entera',
                'descripcion_producto' => 'Litro de leche fresca pasteurizada, proveniente de ganado local, rica en nutrientes y vitaminas.',
                'categoria_id' => 3, // Lácteos y Huevos
                'cantidad' => 100,
                'estado_oferta' => 0,
                'fecha_publicacion' => now(),
                'id_usuario' => 2,
                'imagen_url' => '/ProjectGanadero/public/images/productos/leche_entera.jpg',
                'precio_anterior' => null,
                'precio_unitario' => 3500.00,
            ],
            [
                'id_producto' => 11,
                'nombre_producto' => 'Huevos Orgánicos',
                'descripcion_producto' => 'Cartón de 30 huevos orgánicos de gallinas criadas en campo abierto, sin químicos ni conservantes.',
                'categoria_id' => 3,
                'cantidad' => 50,
                'estado_oferta' => 1,
                'fecha_publicacion' => now(),
                'id_usuario' => 3,
                'imagen_url' => '/ProjectGanadero/public/images/productos/huevos_organicos.jpg',
                'precio_anterior' => 18000.00,
                'precio_unitario' => 15000.00,
            ],
            [
                'id_producto' => 12,
                'nombre_producto' => 'Carne de Res Premium',
                'descripcion_producto' => 'Corte de carne de res de alta calidad, empacada al vacío para garantizar frescura y sabor.',
                'categoria_id' => 4, // Carnes y Embutidos
                'cantidad' => 40,
                'estado_oferta' => 0,
                'fecha_publicacion' => now(),
                'id_usuario' => 4,
                'imagen_url' => '/ProjectGanadero/public/images/productos/carne_res.jpg',
                'precio_anterior' => null,
                'precio_unitario' => 28000.00,
            ],
            [
                'id_producto' => 13,
                'nombre_producto' => 'Queso Campesino',
                'descripcion_producto' => 'Bloque de queso fresco campesino, ideal para acompañar desayunos y arepas.',
                'categoria_id' => 3,
                'cantidad' => 25,
                'estado_oferta' => 1,
                'fecha_publicacion' => now(),
                'id_usuario' => 5,
                'imagen_url' => '/ProjectGanadero/public/images/productos/queso_campesino.jpg',
                'precio_anterior' => 12000.00,
                'precio_unitario' => 10000.00,
            ],
        ]);

    }
}
