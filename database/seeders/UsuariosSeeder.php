<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'id_usuario' => 1,
                'nombre_usuario' => 'JoseD',
                'correo_usuario' => 'josedominguez.121398@gmail.com',
                'contrasena_usuario' => '$2y$10$jqggiIAtb4r0rmIy3hCC1us0PDdkoOdYUqyRiWO9A4ZNXyxb1dvl.', // hash bcrypt
                'direccion_usuario' => null,
                'estado' => 1,
                'fecha_creacion' => '2025-06-03 02:54:18',
                'fecha_ultimo_acceso' => null,
                'id_rol' => 1,
                'nombreCompleto' => 'Jose Dominguez Cuero',
                'telefono_usuario' => null,
                'token_expiracion' => null,
                'token_recuperacion' => null,
            ],
            [
                'id_usuario' => 2,
                'nombre_usuario' => 'admin',
                'correo_usuario' => 'admin@example.com',
                'contrasena_usuario' => 'hashed_password_admin',
                'direccion_usuario' => null,
                'estado' => 'Activo',
                'fecha_creacion' => '2025-06-03 03:04:54',
                'fecha_ultimo_acceso' => null,
                'id_rol' => 1,
                'nombreCompleto' => 'Admin General',
                'telefono_usuario' => 123456789,
                'token_expiracion' => null,
                'token_recuperacion' => null,
            ],
            [
                'id_usuario' => 3,
                'nombre_usuario' => 'empleado1',
                'correo_usuario' => 'empleado1@example.com',
                'contrasena_usuario' => 'hashed_password_empleado',
                'direccion_usuario' => null,
                'estado' => 'Activo',
                'fecha_creacion' => '2025-06-03 03:04:54',
                'fecha_ultimo_acceso' => null,
                'id_rol' => 2,
                'nombreCompleto' => 'Juan Pérez',
                'telefono_usuario' => 987654321,
                'token_expiracion' => null,
                'token_recuperacion' => null,
            ],
            [
                'id_usuario' => 4,
                'nombre_usuario' => 'veterinario1',
                'correo_usuario' => 'veterinario1@example.com',
                'contrasena_usuario' => 'hashed_password_veterinario',
                'direccion_usuario' => null,
                'estado' => 'Activo',
                'fecha_creacion' => '2025-06-03 03:04:54',
                'fecha_ultimo_acceso' => null,
                'id_rol' => 3,
                'nombreCompleto' => 'Dr. Ana Gómez',
                'telefono_usuario' => 555112233,
                'token_expiracion' => null,
                'token_recuperacion' => null,
            ],
            [
                'id_usuario' => 5,
                'nombre_usuario' => 'Juan Santos',
                'correo_usuario' => 'jdsp.1011@gmail.com',
                'contrasena_usuario' => '$2y$10$LyF6yKTx/6jMFHA8OIKpkuSwuhLURmbPjI0Jw8taiKuy4wIQK8EeW.', // hash bcrypt
                'direccion_usuario' => null,
                'estado' => 1,
                'fecha_creacion' => '2025-06-16 21:26:40',
                'fecha_ultimo_acceso' => null,
                'id_rol' => 1,
                'nombreCompleto' => 'Juan David Santos Patiño',
                'telefono_usuario' => null,
                'token_expiracion' => null,
                'token_recuperacion' => null,
            ],
        ]);
        
    }
}
