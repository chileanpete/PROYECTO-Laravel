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
        Schema::create('exportacion_datos', function (Blueprint $table) {
            $table->id('id_exportacion');
            $table->foreignId('id_usuario')->constrained('usuarios', 'id_usuario')->onDelete('cascade');
            $table->string('tipo_exportacion', 50);
            $table->timestamp('fecha_exportacion');
            $table->date('fecha_inicio_datos');
            $table->date('fecha_fin_datos');
            $table->string('archivo_generado', 500);
            $table->boolean('compartido')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('exportaciones_datos');
    }
};
