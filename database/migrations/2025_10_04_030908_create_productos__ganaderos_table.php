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
        Schema::create('productos__ganaderos', function (Blueprint $table) {
            $table->id('id_producto'); // PRIMARY KEY AUTO_INCREMENT
            $table->string('nombre_producto', 255);
            $table->text('descripcion_producto')->nullable();
            
            // Relación con categoriasproducto (FK)
            $table->unsignedBigInteger('categoria_id')->nullable();
            
            $table->integer('cantidad');
            $table->boolean('estado_oferta')->default(0); // tinyint(4)
            $table->dateTime('fecha_publicacion')->useCurrent(); // default current_timestamp()
            
            $table->unsignedBigInteger('id_usuario')->nullable();
            
            $table->string('imagen_url', 255)->nullable();
            $table->decimal('precio_anterior', 10, 2)->nullable();
            $table->decimal('precio_unitario', 10, 2);
            
            
            $table->foreign('categoria_id')->references('id_categoria')->on('categorias_producto')->onDelete('set null');
            $table->foreign('id_usuario')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos__ganaderos');
    }
};
