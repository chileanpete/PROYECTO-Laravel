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
        Schema::create('talleres_recreativos', function (Blueprint $table) {
            $table->id('id_taller');
            $table->string('nombre', 200);
            $table->text('descripcion');
            $table->string('instructor', 100);
            $table->string('categoria', 50);
            $table->integer('duracion_minutos');
            $table->integer('nivel_dificultad')->default(1);
            $table->integer('cupo_maximo');
            $table->decimal('costo', 8, 2)->default(0);
            $table->string('ubicacion', 200);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->boolean('activo')->default(true);
            $table->string('imagen_url', 500)->nullable();
            $table->text('requisitos')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('talleres_recreativos');
    }
};
