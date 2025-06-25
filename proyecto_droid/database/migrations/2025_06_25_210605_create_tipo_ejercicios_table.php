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
        Schema::create('tipos_ejercicio', function (Blueprint $table) {
            $table->id('id_tipo_ejercicio');
            $table->string('nombre', 100);
            $table->string('categoria', 50);
            $table->text('descripcion')->nullable();
            $table->decimal('calorias_por_minuto', 4, 2)->default(0);
            $table->integer('nivel_dificultad')->default(1);
            $table->text('equipamiento_necesario')->nullable();
            $table->string('icono', 100)->nullable();
            $table->text('instrucciones')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipos_ejercicio');
    }
};
