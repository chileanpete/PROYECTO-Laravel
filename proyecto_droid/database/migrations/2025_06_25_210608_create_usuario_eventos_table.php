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
        Schema::create('usuario_eventos', function (Blueprint $table) {
            $table->id('id_usuario_evento');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_evento')->constrained('eventos_academicos', 'id_evento')->onDelete('cascade');
            $table->string('estado', 20)->default('inscrito');
            $table->timestamp('fecha_inscripcion');
            $table->timestamp('fecha_asistencia')->nullable();
            $table->integer('puntos_obtenidos')->default(0);
            $table->timestamps();
            
            $table->unique(['id_usuario', 'id_evento']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_eventos');
    }
};
