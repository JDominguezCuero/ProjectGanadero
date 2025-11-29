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
        Schema::create('stockalimentos', function (Blueprint $table) {
            $table->id('id_stock');
            $table->unsignedBigInteger('cantidad_utilizada');
            $table->timestamp('fecha')->useCurrent();

            // $table->unsignedBigInteger('id_alimento');
            $table->foreignId('id_alimento')
            ->constrained('inventarioalimentos', 'id_alimento')
            ->onDelete('cascade');
            
            $table->unsignedBigInteger('id_animal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stockalimentos');
    }
};
