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
            $table->timestamps('fecha');   
            $table->string('tipo_actividad', 255)->nullable();  
            
            $table->foreign('id_animal')->references('id_animal')->on('animales')->onDelete('set null');

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
