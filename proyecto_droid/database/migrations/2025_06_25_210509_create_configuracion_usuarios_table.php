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
        Schema::create('configuraciones_usuario', function (Blueprint $table) {
            $table->id('id_configuracion');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->boolean('notificaciones_comida')->default(true);
            $table->boolean('notificaciones_ejercicio')->default(true);
            $table->boolean('notificaciones_desafios')->default(true);
            $table->integer('recordatorio_agua_minutos')->default(60);
            $table->string('tema_interfaz', 20)->default('claro');
            $table->string('idioma', 10)->default('es');
            $table->string('privacidad_datos', 20)->default('privado');
            $table->boolean('sincronizacion_calendario')->default(false);
            $table->timestamps();
            
            $table->unique('id_usuario');
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuraciones_usuario');
    }
};
