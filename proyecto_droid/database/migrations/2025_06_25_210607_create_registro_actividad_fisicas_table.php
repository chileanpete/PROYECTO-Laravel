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
        Schema::create('registro_actividad_fisica', function (Blueprint $table) {
            $table->id('id_actividad');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_rutina')->nullable()->constrained('rutinas_ejercicio', 'id_rutina')->onDelete('set null');
            $table->foreignId('id_rutina_ejercicio')->nullable()->constrained('rutinas_ejercicios', 'id_rutina_ejercicio')->onDelete('set null');
            $table->foreignId('id_tipo_ejercicio')->nullable()->constrained('tipos_ejercicio', 'id_tipo_ejercicio')->onDelete('set null');
            $table->date('fecha_actividad');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->integer('duracion_minutos');
            $table->integer('calorias_quemadas');
            $table->integer('intensidad')->default(1);
            $table->text('comentario')->nullable();
            $table->integer('puntos_obtenidos')->default(0);
            $table->boolean('completada')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('registro_actividad_fisica');
    }
};
