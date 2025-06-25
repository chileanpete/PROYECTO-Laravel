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
        Schema::create('eventos_academicos', function (Blueprint $table) {
            $table->id('id_evento');
            $table->string('titulo', 200);
            $table->text('descripcion');
            $table->string('tipo_evento', 50);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('ubicacion', 200);
            $table->string('organizador', 100);
            $table->integer('cupos_disponibles');
            $table->boolean('inscripcion_requerida')->default(true);
            $table->string('url_inscripcion', 500)->nullable();
            $table->boolean('activo')->default(true);
            $table->string('imagen_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('eventos_academicos');
    }
};
