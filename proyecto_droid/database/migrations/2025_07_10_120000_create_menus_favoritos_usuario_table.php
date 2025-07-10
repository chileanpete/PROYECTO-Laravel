<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('menus_favoritos_usuario', function (Blueprint $table) {
            $table->id('id_menu_favorito');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->string('nombre_menu', 100); // Nombre del menú favorito (ej: "Desayuno Saludable", "Almuerzo Proteico")
            $table->text('descripcion')->nullable(); // Descripción opcional del menú
            $table->enum('tipo_comida', ['desayuno', 'almuerzo', 'cena', 'snack'])->nullable(); // Tipo de comida sugerido
            $table->decimal('calorias_totales', 8, 2)->nullable(); // Calorías totales calculadas
            $table->boolean('activo')->default(true); // Si el menú está activo
            $table->integer('veces_usado')->default(0); // Contador de cuántas veces se ha usado
            $table->timestamp('fecha_ultimo_uso')->nullable(); // Última vez que se usó
            $table->timestamps();
            
            // Índices para mejorar rendimiento
            $table->index(['id_usuario', 'activo']);
            $table->index(['tipo_comida']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('menus_favoritos_usuario');
    }
}; 