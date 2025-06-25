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
        Schema::create('menu_diario', function (Blueprint $table) {
            $table->id('id_menu');
            $table->foreignId('id_lugar')->constrained('lugares_comida', 'id_lugar')->onDelete('cascade');
            $table->foreignId('id_plato')->constrained('platos', 'id_plato')->onDelete('cascade');
            $table->date('fecha');
            $table->boolean('disponible')->default(true);
            $table->integer('stock_estimado')->default(0);
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->decimal('precio_especial', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu_diario');
    }
};
