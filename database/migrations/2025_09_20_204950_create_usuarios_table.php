<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id('id_usuario'); 
            $table->string('nombre_usuario', 255)->index(); // INDEX
            $table->string('correo_usuario', 255)->index(); // INDEX
            $table->string('contrasena_usuario', 255);
            $table->string('direccion_usuario', 255)->nullable();
            $table->enum('estado', ['Activo', 'Inactivo', 'Otro'])->nullable();
            $table->timestamp('fecha_creacion')->useCurrent();
            $table->timestamp('fecha_ultimo_acceso')->nullable();
            $table->unsignedBigInteger('id_rol')->nullable();
            $table->string('nombreCompleto', 255)->nullable();
            $table->integer('telefono_usuario')->nullable();
            $table->dateTime('token_expiracion')->nullable();
            $table->string('token_recuperacion', 255)->nullable();
            
            $table->foreign('id_rol')->references('id_rol')->on('roles')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
