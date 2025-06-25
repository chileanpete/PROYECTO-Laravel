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
        Schema::create('rutinas_ejercicio', function (Blueprint $table) {
            $table->id('id_rutina');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->integer('duracion_minutos');
            $table->integer('nivel_dificultad')->default(1);
            $table->string('tipo_rutina', 50);
            $table->integer('calorias_estimadas')->default(0);
            $table->string('creado_por', 50)->default('sistema');
            $table->boolean('activa')->default(true);
            $table->string('imagen_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rutinas_ejercicio');
    }
};
