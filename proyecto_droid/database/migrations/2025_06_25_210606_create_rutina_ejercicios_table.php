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
        Schema::create('rutinas_ejercicios', function (Blueprint $table) {
            $table->id('id_rutina_ejercicio');
            $table->foreignId('id_rutina')->constrained('rutinas_ejercicio', 'id_rutina')->onDelete('cascade');
            $table->foreignId('id_tipo_ejercicio')->constrained('tipos_ejercicio', 'id_tipo_ejercicio')->onDelete('cascade');
            $table->integer('orden_ejercicio');
            $table->integer('duracion_minutos');
            $table->integer('repeticiones')->nullable();
            $table->integer('series')->nullable();
            $table->integer('descanso_segundos')->default(0);
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rutina_ejercicios');
    }
};
