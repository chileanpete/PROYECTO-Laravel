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
        Schema::create('registro_consumo', function (Blueprint $table) {
            $table->id('id_consumo');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_plato')->constrained('platos', 'id_plato')->onDelete('cascade');
            $table->date('fecha_consumo');
            $table->time('hora_consumo');
            $table->decimal('porciones', 3, 2)->default(1);
            $table->integer('calorias_totales');
            $table->integer('valoracion')->nullable();
            $table->text('comentario')->nullable();
            $table->integer('puntos_obtenidos')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registro_consumo');
    }
};
