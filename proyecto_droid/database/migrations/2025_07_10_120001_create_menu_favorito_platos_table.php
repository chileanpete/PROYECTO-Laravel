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
        Schema::create('menu_favorito_platos', function (Blueprint $table) {
            $table->id('id_menu_plato');
            $table->foreignId('id_menu_favorito')->constrained('menus_favoritos_usuario', 'id_menu_favorito')->onDelete('cascade');
            $table->foreignId('id_plato')->constrained('platos', 'id_plato')->onDelete('cascade');
            $table->decimal('cantidad', 8, 2)->default(1.0); // Cantidad del plato (ej: 1.5 porciones)
            $table->string('unidad', 20)->default('porcion'); // Unidad de medida (porcion, gramos, etc.)
            $table->decimal('calorias_porcion', 8, 2)->nullable(); // Calorías de esta porción específica
            $table->text('notas')->nullable(); // Notas específicas para este plato en el menú
            $table->integer('orden')->default(1); // Orden de aparición en el menú
            $table->timestamps();
            
            // Índices
            $table->index(['id_menu_favorito']);
            $table->index(['id_plato']);
            $table->unique(['id_menu_favorito', 'id_plato', 'orden']); // Un plato puede estar múltiples veces pero con orden diferente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('menu_favorito_platos');
    }
}; 