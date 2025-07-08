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
        Schema::create('usuario_desafios', function (Blueprint $table) {
            $table->id('id_usuario_desafio');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_desafio')->constrained('desafios', 'id_desafio')->onDelete('cascade');
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->integer('progreso_actual')->default(0);
            $table->enum('estado', ['inscrito', 'en_progreso', 'completado'])->default('inscrito');
            $table->boolean('completado')->default(false);
            $table->date('fecha_completado')->nullable();
            $table->integer('puntos_obtenidos')->default(0);
            $table->timestamps();
            
            $table->unique(['id_usuario', 'id_desafio']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_desafios');
    }
};
