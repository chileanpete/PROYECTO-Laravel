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
        Schema::create('inscripciones_talleres', function (Blueprint $table) {
            $table->id('id_inscripcion');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->foreignId('id_taller')->constrained('talleres_recreativos', 'id_taller')->onDelete('cascade');
            $table->timestamp('fecha_inscripcion');
            $table->string('estado', 20)->default('inscrito');
            $table->integer('puntos_obtenidos')->default(0);
            $table->integer('calificacion')->nullable();
            $table->text('comentario')->nullable();
            $table->timestamps();
            
            $table->unique(['id_usuario', 'id_taller']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('inscripciones_talleres');
    }
};
