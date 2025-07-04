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
        Schema::create('desafios', function (Blueprint $table) {
            $table->id('id_desafio');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->string('tipo_desafio', 20);
            $table->string('categoria', 30);
            $table->integer('objetivo_valor');
            $table->string('unidad_medida', 20);
            $table->integer('puntos_recompensa');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->integer('dificultad')->default(1);
            $table->string('icono', 100)->nullable();
            $table->json('objetivos_relacionados')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('desafios');
    }
};
