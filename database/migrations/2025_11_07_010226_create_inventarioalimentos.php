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
        Schema::create('inventarioalimentos', function (Blueprint $table) {
            $table->id('id_alimento');
            $table->string('nombre', 255);
            $table->unsignedBigInteger('cantidad');
            $table->string('unidad_medida');
            $table->dateTime('fecha_ingreso')->useCurrent();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventarioalimentos');
    }
};
