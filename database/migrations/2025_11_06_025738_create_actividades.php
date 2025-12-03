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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->unsignedBigInteger('id_animal')->nullable();
            $table->string('descripcion', 255)->nullable();

            // Si necesitas una columna llamada 'fecha' (nullable):
            $table->timestamp('fecha')->nullable();

            // created_at y updated_at (forma correcta)
            $table->timestamps();

            $table->string('tipo_actividad', 255)->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
