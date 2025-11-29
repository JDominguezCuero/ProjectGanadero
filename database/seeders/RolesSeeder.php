<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'id_rol' => 1,
                'nombre_rol' => 'Administrador',
                'descripcion' => 'Acceso total al sistema'
            ],
            [
                'id_rol' => 2,
                'nombre_rol' => 'Empleado',
                'descripcion' => 'Acceso limitado para gestionar ventas y productos'
            ],
            [
                'id_rol' => 3,
                'nombre_rol' => 'Usuario',
                'descripcion' => 'Acceso a la gestión general de inventarios, simulador y productos'
            ],
        ]);
    }
}
