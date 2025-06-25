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
        Schema::create('lugares_comida', function (Blueprint $table) {
            $table->id('id_lugar');
            $table->string('nombre', 100);
            $table->string('tipo', 30);
            $table->string('ubicacion', 200);
            $table->decimal('coordenadas_lat', 10, 8)->nullable();
            $table->decimal('coordenadas_lng', 11, 8)->nullable();
            $table->time('horario_apertura');
            $table->time('horario_cierre');
            $table->string('telefono', 20)->nullable();
            $table->decimal('calificacion_promedio', 3, 2)->default(0);
            $table->decimal('precio_promedio', 8, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->string('imagen_url', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lugares_comida');
    }
};
